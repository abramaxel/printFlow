<?php

namespace App\Services;

use App\Models\{Material, MaterialMovement, MaterialBatch, ServiceBom, PurchaseRequest, OrderItem};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class MrpEngine
{
    /**
     * Helper: Ambil harga material dengan aman (support unit_price & price_per_m2)
     */
    private function getMaterialPrice(Material $material): float
    {
        // Prioritas: unit_price -> price_per_m2 -> price (jika ada) -> 0
        return (float) ($material->unit_price ?? $material->price_per_m2 ?? $material->price ?? 0);
    }

    /**
     * Hitung kebutuhan bahan baku untuk Sales Order
     */
    public function calculateRequirements(Order $order, int $warehouseId = 1): Collection
    {
        $requirements = collect();

        foreach ($order->items as $item) {
            // Cari BOM aktif untuk service ini
            $bom = ServiceBom::where('service_id', $item->service_id)
                ->where('is_active', true)
                ->latest('version')
                ->first();

            if (!$bom) {
                // Fallback: gunakan material_id langsung dari order item
                if ($item->material_id) {
                    $material = Material::find($item->material_id);
                    if ($material) {
                        $requirements->push([
                            'material_id' => $material->id,
                            'material_name' => $material->name,
                            'unit' => $material->unit ?? 'm2',
                            'quantity_needed' => $item->area_m2,
                            'source' => 'direct',
                        ]);
                    }
                }
                continue;
            }

            // Hitung kebutuhan dari BOM
            foreach ($bom->items as $bomItem) {
                $needed = $bomItem->calculateNeed($item->area_m2);
                $requirements->push([
                    'material_id' => $bomItem->material_id,
                    'material_name' => $bomItem->material->name,
                    'unit' => $bomItem->unit,
                    'quantity_needed' => $needed,
                    'source' => 'bom',
                ]);
            }
        }

        return $requirements->groupBy('material_id')->map(function ($items) {
            return [
                'material_id' => $items->first()['material_id'],
                'material_name' => $items->first()['material_name'],
                'unit' => $items->first()['unit'],
                'total_needed' => $items->sum('quantity_needed'),
            ];
        })->values();
    }

    /**
     * Catat movement masuk (pembelian, production receipt)
     * DIPERBAIKI: Menggunakan unit_price sesuai schema database
     */
    public function recordStockIn(
        Material $material,
        float $quantity,
        float $unitCost,
        string $subType = 'purchase_receipt',
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $batchNumber = null,
        ?Carbon $expiryDate = null,
        ?int $userId = null,
        ?string $notes = null
    ): MaterialMovement {
        return DB::transaction(function () use (
            $material, $quantity, $unitCost, $subType,
            $referenceType, $referenceId, $batchNumber, $expiryDate, $userId, $notes
        ) {
            // Auto-generate batch number jika perlu
            if ($material->is_batch_tracked && !$batchNumber) {
                $batchNumber = 'B-' . $material->id . '-' . now()->format('Ymd') . '-' . rand(1000, 9999);
            }

            // Update stok fisik
            $material->increment('stock', $quantity);

            // === PERBAIKAN UTAMA: Update kolom harga yang benar (unit_price) ===
            if ($unitCost > 0) {
                // Cek kolom mana yang ada di database untuk menyimpan harga
                if (\Schema::hasColumn('materials', 'unit_price')) {
                    $material->update(['unit_price' => $unitCost]);
                } elseif (\Schema::hasColumn('materials', 'price_per_m2')) {
                    $material->update(['price_per_m2' => $unitCost]);
                }
                // Jika tidak ada keduanya, skip update harga (hindari error)
            }

            // Catat movement
            $movement = MaterialMovement::create([
                'material_id' => $material->id,
                'user_id' => $userId ?? auth()->id(),
                'movement_type' => 'in',
                'sub_type' => $subType,
                'quantity' => $quantity,
                'unit' => $material->unit ?? 'm2',
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'batch_number' => $batchNumber,
                'expiry_date' => $expiryDate?->toDateString(),
                'notes' => $notes,
                'movement_date' => now(),
            ]);

            // Buat batch record jika batch tracked
            if ($material->is_batch_tracked && $batchNumber) {
                MaterialBatch::create([
                    'material_id' => $material->id,
                    'batch_number' => $batchNumber,
                    'initial_quantity' => $quantity,
                    'remaining_quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'received_date' => now()->toDateString(),
                    'expiry_date' => $expiryDate?->toDateString(),
                    'supplier' => $notes,
                    'status' => 'active',
                ]);
            }

            return $movement;
        });
    }

    /**
     * Catat movement keluar (produksi, rusak)
     */
    public function recordStockOut(
        Material $material,
        float $quantity,
        string $subType = 'production_issue',
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
        ?string $notes = null
    ): array {
        return DB::transaction(function () use (
            $material, $quantity, $subType,
            $referenceType, $referenceId, $userId, $notes
        ) {
            $available = $material->stock - $material->stock_reserved;
            if ($available < $quantity) {
                throw new \Exception("Stok {$material->name} tidak cukup. Tersedia: {$available}, Diminta: {$quantity}");
            }

            $movements = [];
            $remaining = $quantity;
            $totalCost = 0;
            $avgCost = $this->getMaterialPrice($material); // Gunakan helper harga

            if ($material->is_batch_tracked) {
                // FIFO: ambil dari batch tertua
                $batches = MaterialBatch::where('material_id', $material->id)
                    ->where('status', 'active')
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('received_date')
                    ->orderBy('expiry_date')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remaining <= 0) break;

                    $take = min($remaining, (float)$batch->remaining_quantity);
                    $batchCost = $take * (float)$batch->unit_cost;
                    $totalCost += $batchCost;

                    $batch->decrement('remaining_quantity', $take);
                    if ($batch->remaining_quantity <= 0) {
                        $batch->update(['status' => 'depleted']);
                    }

                    $movements[] = MaterialMovement::create([
                        'material_id' => $material->id,
                        'user_id' => $userId ?? auth()->id(),
                        'movement_type' => 'out',
                        'sub_type' => $subType,
                        'quantity' => $take,
                        'unit' => $material->unit ?? 'm2',
                        'unit_cost' => $batch->unit_cost,
                        'total_cost' => $batchCost,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                        'batch_number' => $batch->batch_number,
                        'expiry_date' => $batch->expiry_date,
                        'notes' => $notes,
                        'movement_date' => now(),
                    ]);

                    $remaining -= $take;
                }
            } else {
                // Non-batch: langsung catat
                $totalCost = $quantity * $avgCost;
                $movements[] = MaterialMovement::create([
                    'material_id' => $material->id,
                    'user_id' => $userId ?? auth()->id(),
                    'movement_type' => 'out',
                    'sub_type' => $subType,
                    'quantity' => $quantity,
                    'unit' => $material->unit ?? 'm2',
                    'unit_cost' => $avgCost,
                    'total_cost' => $totalCost,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'notes' => $notes,
                    'movement_date' => now(),
                ]);
            }

            $material->decrement('stock', $quantity);
            return $movements;
        });
    }

    /**
     * Jalankan MRP penuh untuk semua material
     */
    public function runFullMrp(): Collection
    {
        $results = collect();
        $materials = Material::where('is_active', true)->get();

        foreach ($materials as $material) {
            $rop = $material->calculateRop();
            $available = (float)$material->stock - (float)$material->stock_reserved;
            $pipeline = (float)$material->getPipelineDemand();
            $netAvailable = $available - $pipeline;
            
            $status = 'safe';
            $urgency = 'normal';
            $suggestedQty = 0;
            $price = $this->getMaterialPrice($material); // Ambil harga yang benar

            if ($netAvailable <= 0) {
                $status = 'out_of_stock';
                $urgency = 'critical';
                $suggestedQty = $material->maximum_stock > 0 
                    ? $material->maximum_stock 
                    : max(10, $material->minimum_stock * 2);
            } elseif ($netAvailable <= $material->minimum_stock) {
                $status = 'critical';
                $urgency = 'critical';
                $suggestedQty = $material->maximum_stock > 0 
                    ? $material->maximum_stock - $netAvailable 
                    : max(10, $material->minimum_stock * 2 - $netAvailable);
            } elseif ($netAvailable <= $rop) {
                $status = 'reorder_point';
                $urgency = 'warning';
                $suggestedQty = max(10, $material->minimum_stock * 2 - $netAvailable);
            }

            $result = [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'unit' => $material->unit ?? 'm2',
                'physical_stock' => $material->stock,
                'reserved_stock' => $material->stock_reserved,
                'available_stock' => $available,
                'pipeline_demand' => $pipeline,
                'net_available' => $netAvailable,
                'rop' => $rop,
                'minimum_stock' => $material->minimum_stock,
                'maximum_stock' => $material->maximum_stock,
                'avg_daily_usage' => $material->getAverageDailyUsage(30),
                'status' => $status,
                'urgency' => $urgency,
                'suggested_order_qty' => ceil($suggestedQty),
                'estimated_cost' => ceil($suggestedQty) * $price, // Gunakan harga yang benar
                'lead_time_days' => $material->lead_time_days,
                'needed_by_date' => now()->addDays($material->lead_time_days)->toDateString(),
            ];

            $results->push($result);

            // Auto-create purchase request jika critical
            if ($urgency === 'critical' && $suggestedQty > 0) {
                $this->createPurchaseRequest($material, $suggestedQty, $urgency, 'MRP auto-calculation');
            }
        }

        return $results;
    }

    /**
     * Buat Purchase Request
     * DIPERBAIKI: Menggunakan kolom unit_price dan validasi ketat
     */
    private function createPurchaseRequest(
        Material $material,
        float $quantity,
        string $urgency,
        string $reason
    ): PurchaseRequest {
        // Cek apakah sudah ada PR pending untuk material ini
        $existing = PurchaseRequest::where('material_id', $material->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            // Update jika quantity berbeda signifikan (>10%)
            if (abs((float)$existing->requested_quantity - $quantity) > ($quantity * 0.1)) {
                $price = $this->getMaterialPrice($material);
                $existing->update([
                    'requested_quantity' => $quantity,
                    'estimated_cost' => $quantity * $price,
                    'urgency' => $urgency,
                ]);
            }
            return $existing;
        }

        // === PERBAIKAN: Ambil harga & unit dengan aman ===
        $price = $this->getMaterialPrice($material);
        $unit = $material->unit ?? 'm2'; // Fallback ke m2 jika null

        return PurchaseRequest::create([
            'pr_number' => PurchaseRequest::generatePrNumber(),
            'material_id' => $material->id,
            'requested_quantity' => $quantity,
            'unit' => $unit, // Dijamin tidak null
            'estimated_cost' => $quantity * $price,
            'urgency' => $urgency,
            'reason' => $reason,
            'status' => 'pending',
            'requested_by' => auth()->id(),
            'needed_by_date' => now()->addDays($material->lead_time_days ?? 3)->toDateString(),
        ]);
    }
}