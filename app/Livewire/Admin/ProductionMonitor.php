<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Monitor Produksi & Laporan Delay')]
class ProductionMonitor extends Component
{
    public string $search = '';
    public string $bucket = 'all';
    public string $stageFilter = 'all';
    public string $sort = 'severity';

    public ?int $revisionId = null;
    public string $newDeadline = '';
    public string $revisionReason = '';

    public ?int $escalateId = null;
    public string $escalateNote = '';

    public ?int $resolveId = null;
    public string $resolveNote = '';

    public array $incCols = [];
    public bool $hasHistory = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin'], true)) {
            abort(403, 'Monitor produksi & laporan delay hanya untuk owner.');
        }

        $this->hasHistory = Schema::hasTable('order_histories');
        $this->incCols = Schema::hasTable('production_incidents') ? Schema::getColumnListing('production_incidents') : [];
    }

    private function stages(): array
    {
        $cfg = (array) config('app.stage_sla', []);

        $base = [
            'waiting_invoice' => ['Menunggu Invoice', 4,  'Admin belum menerbitkan invoice',        'admin'],
            'unpaid'          => ['Belum Dibayar',    24, 'Pelanggan belum menyelesaikan bayar',    'customer'],
            'paid'            => ['Siap Produksi',    2,  'Produksi belum ambil bahan & cetak',     'production'],
            'processing'      => ['Sedang Dicetak',   24, 'Cetak melewati anggaran waktu',          'production'],
            'qc'              => ['Quality Control',  4,  'QC belum diselesaikan',                  'production'],
            'shipping'        => ['Siap / Dikirim',   24, 'Pengiriman belum tuntas',                'logistics'],
        ];

        foreach ($base as $k => $v) {
            if (isset($cfg[$k]) && (float) $cfg[$k] > 0) $base[$k][1] = (float) $cfg[$k];
        }

        return $base;
    }

    private function fmtDur(int $sec): string
    {
        $sec = max(0, $sec);
        $d = intdiv($sec, 86400);
        $h = intdiv($sec % 86400, 3600);
        $m = intdiv($sec % 3600, 60);
        if ($d > 0) return $d . ' hari ' . $h . ' jam';
        if ($h > 0) return $h . ' jam ' . $m . ' mnt';
        return $m . ' mnt';
    }

    private function incText($inc, array $keys, string $fallback): string
    {
        foreach ($keys as $k) {
            $v = data_get($inc, $k);
            if (is_string($v) && trim($v) !== '') return trim($v);
        }
        return $fallback;
    }

    private function isOpenIncident($inc): bool
    {
        $s = strtolower((string) (data_get($inc, 'status') ?? 'open'));
        return !in_array($s, ['resolved', 'closed', 'done', 'selesai', 'cancelled'], true);
    }

    private function waOf($user): string
    {
        $d = preg_replace('/\D+/', '', (string) ($user->phone ?? '')) ?? '';
        if ($d === '') return '';
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d;
    }

    private function collectRows(): array
    {
        $stages = $this->stages();

        $orders = Order::with(['user', 'items', 'invoice', 'incidents'])
            ->whereNotIn('status', ['completed', 'cancelled', 'rejected'])
            ->get();

        $ids = $orders->pluck('id')->all();
        $hist = [];
        if ($this->hasHistory && !empty($ids)) {
            $hrows = DB::table('order_histories')->whereIn('order_id', $ids)->orderByDesc('id')->get();
            foreach ($hrows as $h) {
                $oid = (int) $h->order_id;
                if (!isset($hist[$oid])) $hist[$oid] = $h;
            }
        }

        $rows = [];
        $now = now();

        foreach ($orders as $o) {
            $status = (string) $o->status;
            if (!isset($stages[$status])) continue;

            [$label, $budget, $cause, $owner] = $stages[$status];

            if ($status === 'processing') {
                $days = (int) ($o->items->max('duration_days') ?? 0);
                if ($days > 0) $budget = $days * 24;
            }

            $since = null;
            $h = $hist[$o->id] ?? null;
            if ($h) {
                $to = null;
                foreach (['to_status', 'new_status', 'status'] as $k) {
                    if (isset($h->{$k})) { $to = (string) $h->{$k}; break; }
                }
                if ($to === $status && !empty($h->created_at)) {
                    $since = Carbon::parse($h->created_at);
                }
            }
            if (!$since) $since = $o->created_at ? Carbon::parse($o->created_at) : $now->copy();

            $dwellSec = max(0, $now->timestamp - $since->timestamp);
            $budgetSec = (int) round($budget * 3600);
            $stuck = $dwellSec > $budgetSec;
            $overBudgetSec = max(0, $dwellSec - $budgetSec);

            $deadline = $o->deadline_date ? Carbon::parse($o->deadline_date)->endOfDay() : null;
            $overdueSec = $deadline ? $now->timestamp - $deadline->timestamp : null;
            $isLate = $overdueSec !== null && $overdueSec > 0;
            $remainSec = $deadline ? $deadline->timestamp - $now->timestamp : null;
            $isRisk = !$isLate && $remainSec !== null && $remainSec <= 86400;

            $open = [];
            foreach (($o->incidents ?? collect()) as $inc) {
                if (!$this->isOpenIncident($inc)) continue;
                $open[] = [
                    'id' => (int) data_get($inc, 'id'),
                    'label' => $this->incText($inc, ['reason', 'title', 'type', 'category'], 'Kendala produksi'),
                    'note' => $this->incText($inc, ['description', 'notes', 'note'], ''),
                    'severity' => (string) (data_get($inc, 'severity') ?? 'normal'),
                    'created' => data_get($inc, 'created_at') ? Carbon::parse(data_get($inc, 'created_at'))->format('d M Y, H:i') : '—',
                ];
            }

            if (!$isLate && !$isRisk && !$stuck && empty($open)) continue;

            $inv = $o->invoice;
            $total = (float) ($inv->total_amount ?? 0);
            $paid = (float) ($inv->total_paid ?? 0);

            $score = 0;
            if ($isLate) $score += 100000 + (int) round($overdueSec / 3600);
            if ($stuck) $score += 50000 + (int) round($overBudgetSec / 3600);
            if ($isRisk) $score += 20000;
            $score += count($open) * 5000;
            if ((string) ($o->priority ?? '') === 'high') $score += 2000;

            $rows[] = [
                'id' => (int) $o->id,
                'invoice' => (string) $o->invoice_number,
                'customer' => (string) ($o->user->name ?? 'User dihapus'),
                'wa' => $this->waOf($o->user),
                'status' => $status,
                'stage' => $label,
                'cause' => $cause,
                'owner_side' => $owner,
                'priority' => (string) ($o->priority ?? 'normal'),
                'since' => $since->format('d M Y, H:i'),
                'dwell' => $this->fmtDur($dwellSec),
                'dwell_sec' => $dwellSec,
                'budget' => $this->fmtDur($budgetSec),
                'budget_sec' => $budgetSec,
                'budget_pct' => $budgetSec > 0 ? min(100, (int) round($dwellSec / $budgetSec * 100)) : 100,
                'stuck' => $stuck,
                'over_budget' => $this->fmtDur($overBudgetSec),
                'deadline' => $o->deadline_date ? Carbon::parse($o->deadline_date)->format('d M Y') : null,
                'deadline_raw' => $o->deadline_date ? Carbon::parse($o->deadline_date)->format('Y-m-d') : '',
                'late' => $isLate,
                'risk' => $isRisk,
                'overdue' => $isLate ? $this->fmtDur((int) $overdueSec) : null,
                'remain' => (!$isLate && $remainSec !== null) ? $this->fmtDur((int) $remainSec) : null,
                'overdue_sec' => $isLate ? (int) $overdueSec : 0,
                'total' => $total,
                'outstanding' => max(0, round($total - $paid, 2)),
                'incidents' => $open,
                'incident_count' => count($open),
                'items_count' => $o->items->count(),
                'service' => (string) ($o->items->first()?->service?->name ?? '—'),
                'score' => $score,
            ];
        }

        return $rows;
    }

    private function filtered(array $rows): array
    {
        $term = mb_strtolower(trim($this->search));

        $out = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->bucket === 'late' && !$r['late']) return false;
            if ($this->bucket === 'risk' && !$r['risk']) return false;
            if ($this->bucket === 'stuck' && !$r['stuck']) return false;
            if ($this->bucket === 'incident' && $r['incident_count'] === 0) return false;
            if ($this->stageFilter !== 'all' && $r['status'] !== $this->stageFilter) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['invoice']), $term)
                || str_contains(mb_strtolower($r['customer']), $term)
                || str_contains(mb_strtolower($r['service']), $term);
        }));

        usort($out, function ($a, $b) {
            if ($this->sort === 'deadline') return [$a['deadline_raw'] ?: '9999', -$b['score']] <=> [$b['deadline_raw'] ?: '9999', -$a['score']];
            if ($this->sort === 'dwell') return $b['dwell_sec'] <=> $a['dwell_sec'];
            if ($this->sort === 'money') return $b['outstanding'] <=> $a['outstanding'];
            return $b['score'] <=> $a['score'];
        });

        return $out;
    }

    public function setBucket(string $v): void { $this->bucket = $v; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->bucket = 'all';
        $this->stageFilter = 'all';
        $this->sort = 'severity';
    }

    /* ---------- AKSI OWNER ---------- */

    public function openRevision($id): void
    {
        $o = Order::find($id);
        if (!$o) return;
        $this->revisionId = (int) $o->id;
        $this->newDeadline = $o->deadline_date ? Carbon::parse($o->deadline_date)->format('Y-m-d') : now()->addDays(3)->format('Y-m-d');
        $this->revisionReason = '';
        $this->resetErrorBag(['newDeadline', 'revisionReason']);
    }

    public function closeRevision(): void
    {
        $this->revisionId = null;
        $this->revisionReason = '';
        $this->resetErrorBag(['newDeadline', 'revisionReason']);
    }

    public function saveRevision(): void
    {
        $this->validate([
            'newDeadline' => 'required|date',
            'revisionReason' => 'required|string|min:5|max:300',
        ], [
            'newDeadline.required' => 'Tanggal deadline baru wajib diisi.',
            'newDeadline.date' => 'Format tanggal tidak valid.',
            'revisionReason.required' => 'Alasan revisi wajib diisi agar tercatat di jejak audit.',
            'revisionReason.min' => 'Alasan minimal 5 karakter.',
        ]);

        $o = Order::find($this->revisionId);
        if (!$o) { $this->closeRevision(); return; }

        $old = $o->deadline_date ? Carbon::parse($o->deadline_date)->format('d M Y') : 'tanpa deadline';
        $new = Carbon::parse($this->newDeadline);

        $line = '[REVISI DEADLINE ' . now()->format('d M Y H:i') . '] ' . $old . ' → ' . $new->format('d M Y')
            . ' oleh ' . (auth()->user()->name ?? 'Owner') . ' — ' . trim($this->revisionReason);

        DB::transaction(function () use ($o, $new, $line) {
            $o->deadline_date = $new->toDateString();
            $o->notes = trim((string) $o->notes) === '' ? $line : trim((string) $o->notes) . "\n" . $line;
            $o->save();
        });

        $inv = $o->invoice_number;
        $this->closeRevision();
        session()->flash('pm_success', 'Deadline ' . $inv . ' direvisi menjadi ' . $new->format('d M Y') . '. Alasan tercatat.');
    }

    public function openEscalate($id): void
    {
        $o = Order::find($id);
        if (!$o) return;
        $this->escalateId = (int) $o->id;
        $this->escalateNote = '';
        $this->resetErrorBag('escalateNote');
    }

    public function closeEscalate(): void
    {
        $this->escalateId = null;
        $this->escalateNote = '';
        $this->resetErrorBag('escalateNote');
    }

    public function saveEscalate(): void
    {
        $this->validate([
            'escalateNote' => 'required|string|min:5|max:400',
        ], [
            'escalateNote.required' => 'Tuliskan instruksi eskalasi untuk tim.',
            'escalateNote.min' => 'Instruksi minimal 5 karakter.',
        ]);

        $o = Order::find($this->escalateId);
        if (!$o) { $this->closeEscalate(); return; }

        $note = trim($this->escalateNote);
        $line = '[ESKALASI OWNER ' . now()->format('d M Y H:i') . '] ' . $note . ' (oleh ' . (auth()->user()->name ?? 'Owner') . ')';

        DB::transaction(function () use ($o, $note, $line) {
            $o->priority = 'high';
            $o->notes = trim((string) $o->notes) === '' ? $line : trim((string) $o->notes) . "\n" . $line;
            $o->save();

            if (!empty($this->incCols)) {
                $map = [
                    'order_id' => $o->id,
                    'reported_by' => auth()->id(),
                    'user_id' => auth()->id(),
                    'type' => 'escalation',
                    'category' => 'escalation',
                    'reason' => 'Eskalasi Owner',
                    'title' => 'Eskalasi Owner',
                    'description' => $note,
                    'notes' => $note,
                    'note' => $note,
                    'status' => 'open',
                    'severity' => 'high',
                ];
                $data = [];
                foreach ($map as $k => $v) {
                    if (in_array($k, $this->incCols, true) && !is_null($v)) $data[$k] = $v;
                }
                if (in_array('created_at', $this->incCols, true)) $data['created_at'] = now();
                if (in_array('updated_at', $this->incCols, true)) $data['updated_at'] = now();
                if (isset($data['order_id'])) DB::table('production_incidents')->insert($data);
            }
        });

        $inv = $o->invoice_number;
        $this->closeEscalate();
        session()->flash('pm_success', 'Pesanan ' . $inv . ' dieskalasi. Prioritas tinggi & instruksi tercatat untuk tim produksi.');
    }

    public function openResolve($incidentId): void
    {
        if (empty($this->incCols)) return;
        $row = DB::table('production_incidents')->find($incidentId);
        if (!$row) return;
        $this->resolveId = (int) $incidentId;
        $this->resolveNote = '';
        $this->resetErrorBag('resolveNote');
    }

    public function closeResolve(): void
    {
        $this->resolveId = null;
        $this->resolveNote = '';
        $this->resetErrorBag('resolveNote');
    }

    public function saveResolve(): void
    {
        $this->validate([
            'resolveNote' => 'required|string|min:5|max:400',
        ], [
            'resolveNote.required' => 'Jelaskan bagaimana kendala diselesaikan.',
            'resolveNote.min' => 'Penjelasan minimal 5 karakter.',
        ]);

        $row = DB::table('production_incidents')->find($this->resolveId);
        if (!$row) { $this->closeResolve(); return; }

        $note = trim($this->resolveNote);
        $data = ['status' => 'resolved'];
        foreach (['resolved_by', 'closed_by'] as $c) if (in_array($c, $this->incCols, true)) $data[$c] = auth()->id();
        foreach (['resolved_at', 'closed_at'] as $c) if (in_array($c, $this->incCols, true)) $data[$c] = now();
        if (in_array('updated_at', $this->incCols, true)) $data['updated_at'] = now();

        foreach (['description', 'notes', 'note'] as $c) {
            if (in_array($c, $this->incCols, true)) {
                $old = (string) ($row->{$c} ?? '');
                $data[$c] = mb_substr(trim($old . "\n[SELESAI " . now()->format('d M Y H:i') . '] ' . $note), 0, 1000);
                break;
            }
        }

        DB::table('production_incidents')->where('id', $this->resolveId)->update($data);

        $this->closeResolve();
        session()->flash('pm_success', 'Kendala ditandai selesai dan tercatat di jejak pesanan.');
    }

    /* ---------- EKSPOR ---------- */

    private function exportRows(): array
    {
        return $this->filtered($this->collectRows());
    }

    public function exportExcel()
    {
        $rows = $this->exportRows();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0A3F33" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="n"><NumberFormat ss:Format="#,##0.00"/></Style></Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Laporan Delay"><Table>' . "\n";

        $head = ['Invoice', 'Pelanggan', 'Tahap', 'Sejak Tahap', 'Dwell', 'Anggaran', 'Macet', 'Deadline', 'Keterlambatan', 'Tagihan', 'Sisa Tagihan', 'Kendala Terbuka', 'Prioritas', 'Layanan'];
        $xml .= '<Row>';
        foreach ($head as $h) $xml .= '<Cell ss:StyleID="h"><Data ss:Type="String">' . htmlspecialchars($h, ENT_XML1) . '</Data></Cell>';
        $xml .= '</Row>' . "\n";

        foreach ($rows as $r) {
            $cells = [
                ['String', $r['invoice']],
                ['String', $r['customer']],
                ['String', $r['stage']],
                ['String', $r['since']],
                ['String', $r['dwell']],
                ['String', $r['budget']],
                ['String', $r['stuck'] ? 'YA' : 'tidak'],
                ['String', $r['deadline'] ?? '—'],
                ['String', $r['overdue'] ?? ($r['remain'] ? 'sisa ' . $r['remain'] : '—')],
                ['Number', $r['total']],
                ['Number', $r['outstanding']],
                ['Number', $r['incident_count']],
                ['String', $r['priority']],
                ['String', $r['service']],
            ];
            $xml .= '<Row>';
            foreach ($cells as [$t, $v]) {
                $style = $t === 'Number' ? ' ss:StyleID="n"' : '';
                $xml .= '<Cell' . $style . '><Data ss:Type="' . $t . '">' . htmlspecialchars((string) $v, ENT_XML1) . '</Data></Cell>';
            }
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response()->streamDownload(function () use ($xml) { echo $xml; }, 'laporan-delay-' . now()->format('Ymd-His') . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportCsv()
    {
        $rows = $this->exportRows();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Invoice', 'Pelanggan', 'Tahap', 'Sejak Tahap', 'Dwell', 'Anggaran', 'Macet', 'Deadline', 'Keterlambatan', 'Tagihan', 'Sisa Tagihan', 'Kendala Terbuka', 'Prioritas', 'Layanan'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['invoice'], $r['customer'], $r['stage'], $r['since'], $r['dwell'], $r['budget'],
                    $r['stuck'] ? 'YA' : 'tidak', $r['deadline'] ?? '-',
                    $r['overdue'] ?? ($r['remain'] ? 'sisa ' . $r['remain'] : '-'),
                    number_format($r['total'], 2, '.', ''), number_format($r['outstanding'], 2, '.', ''),
                    $r['incident_count'], $r['priority'], $r['service'],
                ], ';');
            }
            fclose($out);
        }, 'laporan-delay-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $all = $this->collectRows();
        $rows = $this->filtered($all);

        $late = 0; $risk = 0; $stuck = 0; $inc = 0; $money = 0.0; $lateSec = 0; $maxLate = 0;
        $perStage = [];
        foreach ($all as $r) {
            if ($r['late']) { $late++; $lateSec += $r['overdue_sec']; $maxLate = max($maxLate, $r['overdue_sec']); }
            if ($r['risk']) $risk++;
            if ($r['stuck']) $stuck++;
            $inc += $r['incident_count'];
            $money += $r['outstanding'];
            if (!isset($perStage[$r['status']])) $perStage[$r['status']] = ['label' => $r['stage'], 'cause' => $r['cause'], 'side' => $r['owner_side'], 'count' => 0];
            $perStage[$r['status']]['count']++;
        }

        $maxStage = $perStage ? max(array_column($perStage, 'count')) : 0;
        $bottlenecks = [];
        foreach ($perStage as $k => $v) {
            $bottlenecks[] = $v + ['key' => $k, 'bar' => $maxStage > 0 ? max(5, (int) round($v['count'] / $maxStage * 100)) : 5];
        }
        usort($bottlenecks, fn($a, $b) => $b['count'] <=> $a['count']);

        $stages = [];
        foreach ($this->stages() as $k => $v) $stages[] = ['key' => $k, 'label' => $v[0]];

        $revTarget = null;
        if ($this->revisionId) {
            $o = Order::find($this->revisionId);
            if ($o) $revTarget = ['invoice' => (string) $o->invoice_number, 'deadline' => $o->deadline_date ? Carbon::parse($o->deadline_date)->format('d M Y') : 'tanpa deadline'];
        }

        $escTarget = null;
        if ($this->escalateId) {
            $o = Order::find($this->escalateId);
            if ($o) $escTarget = ['invoice' => (string) $o->invoice_number, 'customer' => (string) ($o->user->name ?? '-')];
        }

        $resTarget = null;
        if ($this->resolveId && !empty($this->incCols)) {
            $r = DB::table('production_incidents')->find($this->resolveId);
            if ($r) {
                $resTarget = [
                    'label' => $this->incText($r, ['reason', 'title', 'type', 'category'], 'Kendala produksi'),
                    'invoice' => (string) (DB::table('orders')->where('id', $r->order_id)->value('invoice_number') ?? '-'),
                ];
            }
        }

        return view('livewire.admin.production-monitor', [
            'rows' => $rows,
            'stages' => $stages,
            'bottlenecks' => $bottlenecks,
            'revTarget' => $revTarget,
            'escTarget' => $escTarget,
            'resTarget' => $resTarget,
            'stats' => [
                'total' => count($all),
                'shown' => count($rows),
                'late' => $late,
                'risk' => $risk,
                'stuck' => $stuck,
                'incident' => $inc,
                'money' => round($money, 2),
                'avg_late' => $late > 0 ? $this->fmtDur((int) round($lateSec / $late)) : '—',
                'max_late' => $maxLate > 0 ? $this->fmtDur($maxLate) : '—',
            ],
            'hasFilter' => $this->search !== '' || $this->bucket !== 'all' || $this->stageFilter !== 'all',
        ]);
    }
}