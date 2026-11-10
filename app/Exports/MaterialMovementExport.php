<?php

namespace App\Exports;

use App\Models\MaterialMovement;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialMovementExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnFormatting
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    // 1. Query data (tetap sama, hemat memori)
    public function query(): Builder
    {
        return MaterialMovement::query()
            ->with(['material', 'user'])
            ->when($this->filters['material_id'] ?? null, fn($q, $id) => $q->where('material_id', $id))
            ->when($this->filters['movement_type'] ?? null, fn($q, $type) => $q->where('movement_type', $type))
            ->when($this->filters['date_from'] ?? null, fn($q, $date) => $q->whereDate('movement_date', '>=', $date))
            ->when($this->filters['date_to'] ?? null, fn($q, $date) => $q->whereDate('movement_date', '<=', $date))
            ->orderBy('movement_date', 'desc');
    }

    // 2. Judul Sheet
    public function title(): string
    {
        return 'Audit Trail Stok';
    }

    // 3. Header Kolom
    public function headings(): array
    {
        return [
            'Tanggal & Waktu',
            'Nama Material',
            'Tipe Movement',
            'Sub-Tipe',
            'Qty',
            'Satuan',
            'Nilai (Rp)',
            'Referensi',
            'Catatan',
            'Oleh (User)'
        ];
    }

    // 4. Format Data per Baris
    public function map($movement): array
    {
        return [
            $movement->movement_date, // Biarkan objek Carbon, nanti diformat oleh WithColumnFormatting
            $movement->material->name ?? 'N/A',
            strtoupper($movement->movement_type),
            str_replace('_', ' ', ucfirst($movement->sub_type)),
            $movement->quantity,
            $movement->unit,
            $movement->total_cost ?? 0, // Biarkan angka, nanti diformat jadi Rp
            $movement->reference_type ? "{$movement->reference_type} #{$movement->reference_id}" : '-',
            $movement->notes ?? '-',
            $movement->user->name ?? 'Sistem',
        ];
    }

    // 5. STYLING (Membuat Excel Rapi & Profesional)
    // PERBAIKAN: Menambahkan return type ": ?array"
    public function styles(Worksheet $sheet): ?array
    {
        // Styling untuk Baris 1 (Header)
        $sheet->getStyle('1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'], // Teks putih
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '2563EB'], // Background Biru (Tailwind blue-600)
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Styling Border untuk seluruh tabel data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:J{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'E5E7EB'], // Abu-abu muda
                ],
            ],
        ]);

        // Mengatur Lebar Kolom agar tidak terlalu sempit/lebar
        $sheet->getColumnDimension('A')->setWidth(20); // Tanggal
        $sheet->getColumnDimension('B')->setWidth(25); // Nama Material
        $sheet->getColumnDimension('C')->setWidth(15); // Tipe
        $sheet->getColumnDimension('D')->setWidth(15); // Sub-Tipe
        $sheet->getColumnDimension('E')->setWidth(10); // Qty
        $sheet->getColumnDimension('F')->setWidth(10); // Satuan
        $sheet->getColumnDimension('G')->setWidth(18); // Nilai
        $sheet->getColumnDimension('H')->setWidth(20); // Referensi
        $sheet->getColumnDimension('I')->setWidth(35); // Catatan (dibuat lebar)
        $sheet->getColumnDimension('J')->setWidth(20); // Oleh

        // Center alignment untuk kolom tertentu
        $sheet->getStyle('C1:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return []; // Mengembalikan array kosong (valid sesuai kontrak ?array)
    }

    // 6. FORMATTING KOLOM (Agar Excel mengenali sebagai Tanggal & Mata Uang)
    public function columnFormats(): array
    {
        return [
            'A' => 'dd/mm/yyyy hh:mm', // Format Tanggal & Jam
            'G' => '"Rp" #,##0',       // Format Mata Uang Rupiah
        ];
    }
}