<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('CRM Pelanggan')]
class Customers extends Component
{
    use WithPagination;

    public string $search = '';
    public string $tierFilter = 'all';
    public string $segmentFilter = 'all';
    public string $audienceFilter = 'all';
    public string $statusFilter = 'all';
    public string $sort = 'value';

    public string $targetCategory = 'all';
    public string $targetAudience = 'all';

    public ?int $detailId = null;

    public ?int $editId = null;
    public string $eName = '';
    public string $eEmail = '';
    public string $ePhone = '';
    public string $eTier = 'regular';
    public string $eStatus = 'active';
    public string $eAddress = '';
    public string $eAddressDetail = '';
    public string $eNotes = '';

    public bool $createOpen = false;
    public string $cName = '';
    public string $cEmail = '';
    public string $cPhone = '';
    public string $cTier = 'regular';
    public string $cAddress = '';

    public array $userCols = [];
    public bool $ordersSoft = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin'], true)) {
            abort(403, 'CRM pelanggan hanya untuk owner.');
        }
        $this->userCols = Schema::getColumnListing('users');
        $this->ordersSoft = Schema::hasColumn('orders', 'deleted_at');
    }

    private function rules(): array
    {
        $c = (array) config('app.crm', []);
        return [
            'vip' => (float) ($c['vip'] ?? 25000000),
            'gold' => (float) ($c['gold'] ?? 10000000),
            'silver' => (float) ($c['silver'] ?? 3000000),
            'champion_orders' => (int) ($c['champion_orders'] ?? 5),
            'champion_value' => (float) ($c['champion_value'] ?? 5000000),
            'loyal_orders' => (int) ($c['loyal_orders'] ?? 3),
            'dormant' => (int) ($c['dormant_days'] ?? 90),
            'idle' => (int) ($c['idle_days'] ?? 45),
            'new_days' => (int) ($c['new_days'] ?? 30),
            'active' => (int) ($c['active_days'] ?? 30),
        ];
    }

    private function personalDomains(): array
    {
        return ['gmail.com','googlemail.com','yahoo.com','yahoo.co.id','ymail.com','hotmail.com','outlook.com','live.com','icloud.com','me.com','proton.me','protonmail.com','aol.com','mail.com','gmx.com','zoho.com'];
    }

    private function businessWords(): array
    {
        return ['pt ','cv ','tbk','org','toko','studio','print','media','jaya','group','company','co.','inc','llc','creative','agency','digital','percetakan','percetakan','kop','koperasi','yayasan','sekolah','kampus','universitas'];
    }

    private function audienceOf($u): string
    {
        $email = strtolower((string) $u->email);
        $domain = str_contains($email, '@') ? substr($email, strrpos($email, '@') + 1) : '';
        $name = strtolower((string) $u->name);

        if ($domain === '' || str_ends_with($domain, 'guest.local')) return 'personal';
        if (!in_array($domain, $this->personalDomains(), true)) return 'business';

        foreach ($this->businessWords() as $w) {
            if ($w !== '' && str_contains($name, $w)) return 'business';
        }
        return 'personal';
    }

    private function segmentOf(int $orders, float $paid, ?int $recency, int $tenure, array $r): string
    {
        if ($orders === 0) return 'prospek';
        if ($recency !== null && $recency > $r['dormant']) return 'hilang';
        if ($recency !== null && $recency > $r['idle']) return 'pasif';
        if ($orders >= $r['champion_orders'] && $paid >= $r['champion_value']) return 'champion';
        if ($orders >= $r['loyal_orders'] || $paid >= $r['silver']) return 'loyal';
        if ($tenure <= $r['new_days']) return 'baru';
        return 'reguler';
    }

    private function recommendTier(float $paid, string $status, array $r): string
    {
        if ($status === 'suspended') return 'inactive';
        if ($paid >= $r['vip']) return 'vip';
        if ($paid >= $r['gold']) return 'gold';
        if ($paid >= $r['silver']) return 'silver';
        return 'regular';
    }

    private function waOf($u): string
    {
        $d = preg_replace('/\D+/', '', (string) ($u->phone ?? '')) ?? '';
        if ($d === '') return '';
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d;
    }

    private function buildProfiles(): array
    {
        $r = $this->rules();
        $cats = Category::pluck('name', 'id')->all();
        $users = User::where('role', 'customer')->get();

        /* Agregat pesanan */
        $orderAgg = [];
        foreach (Order::query()
            ->selectRaw('user_id as uid, status, shipping_method')
            ->selectRaw('COUNT(*) as c, MIN(created_at) as first_at, MAX(created_at) as last_at')
            ->groupBy('user_id', 'status', 'shipping_method')->get() as $row) {
            $uid = (int) $row->uid;
            if (!isset($orderAgg[$uid])) {
                $orderAgg[$uid] = ['total' => 0, 'active' => 0, 'done' => 0, 'cancel' => 0, 'pickup' => 0, 'deliver' => 0, 'first' => null, 'last' => null];
            }
            $a = &$orderAgg[$uid];
            $c = (int) $row->c;
            $a['total'] += $c;
            if (in_array($row->status, ['waiting_invoice','unpaid','paid','processing','qc','shipping'], true)) $a['active'] += $c;
            if ($row->status === 'completed') $a['done'] += $c;
            if (in_array($row->status, ['cancelled','rejected'], true)) $a['cancel'] += $c;
            if ($row->shipping_method === 'pickup') $a['pickup'] += $c; else $a['deliver'] += $c;
            if ($row->first_at && ($a['first'] === null || $row->first_at < $a['first'])) $a['first'] = $row->first_at;
            if ($row->last_at && ($a['last'] === null || $row->last_at > $a['last'])) $a['last'] = $row->last_at;
            unset($a);
        }

        /* Agregat tagihan */
        $billAgg = Invoice::query()
            ->join('orders', 'orders.id', '=', 'invoices.order_id')
            ->when($this->ordersSoft, fn($q) => $q->whereNull('orders.deleted_at'))
            ->selectRaw('orders.user_id as uid')
            ->selectRaw('COUNT(*) as inv_count')
            ->selectRaw('COALESCE(SUM(invoices.total_amount),0) as billed')
            ->selectRaw('COALESCE(SUM(invoices.total_paid),0) as paid')
            ->selectRaw("SUM(CASE WHEN invoices.status='paid' THEN 1 ELSE 0 END) as lunas")
            ->groupBy('orders.user_id')->get()->keyBy('uid')->all();

        /* Tren 30 hari */
        $curStart = now()->subDays(30)->toDateTimeString();
        $prevStart = now()->subDays(60)->toDateTimeString();
        $trendAgg = Invoice::query()
            ->join('orders', 'orders.id', '=', 'invoices.order_id')
            ->when($this->ordersSoft, fn($q) => $q->whereNull('orders.deleted_at'))
            ->selectRaw('orders.user_id as uid')
            ->selectRaw('COALESCE(SUM(CASE WHEN orders.created_at >= ? THEN invoices.total_paid ELSE 0 END),0) as cur', [$curStart])
            ->selectRaw('COALESCE(SUM(CASE WHEN orders.created_at >= ? AND orders.created_at < ? THEN invoices.total_paid ELSE 0 END),0) as prev', [$prevStart, $curStart])
            ->groupBy('orders.user_id')->get()->keyBy('uid')->all();

        /* Kebiasaan: divisi, layanan, SLA */
        $itemRows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'services.id', '=', 'order_items.service_id')
            ->when($this->ordersSoft, fn($q) => $q->whereNull('orders.deleted_at'))
            ->selectRaw('orders.user_id as uid, services.category_id as cid, services.name as sname, order_items.duration_name as dname')
            ->selectRaw('COUNT(*) as lines, COALESCE(SUM(order_items.final_subtotal),0) as value, COALESCE(SUM(order_items.area_m2),0) as area')
            ->groupBy('orders.user_id', 'services.category_id', 'services.name', 'order_items.duration_name')
            ->get();

        $habit = [];
        foreach ($itemRows as $row) {
            $uid = (int) $row->uid;
            if (!isset($habit[$uid])) $habit[$uid] = ['cat' => [], 'svc' => [], 'sla' => [], 'area' => 0.0, 'lines' => 0];
            $h = &$habit[$uid];
            $cn = (string) ($cats[$row->cid] ?? 'Tanpa divisi');
            $sn = (string) $row->sname;
            $dn = (string) ($row->dname ?? 'Normal');
            $v = (float) $row->value;
            $l = (int) $row->lines;
            $h['cat'][$cn] = ($h['cat'][$cn] ?? 0) + $v;
            $h['svc'][$sn] = ($h['svc'][$sn] ?? 0) + $l;
            $h['sla'][$dn] = ($h['sla'][$dn] ?? 0) + $l;
            $h['area'] += (float) $row->area;
            $h['lines'] += $l;
            unset($h);
        }

        $profiles = [];
        foreach ($users as $u) {
            $o = $orderAgg[$u->id] ?? ['total'=>0,'active'=>0,'done'=>0,'cancel'=>0,'pickup'=>0,'deliver'=>0,'first'=>null,'last'=>null];
            $b = $billAgg[$u->id] ?? null;
            $t = $trendAgg[$u->id] ?? null;
            $h = $habit[$u->id] ?? ['cat'=>[],'svc'=>[],'sla'=>[],'area'=>0.0,'lines'=>0];

            $billed = (float) ($b->billed ?? 0);
            $paid = (float) ($b->paid ?? 0);
            $invCount = (int) ($b->inv_count ?? 0);
            $lunas = (int) ($b->lunas ?? 0);

            $first = $o['first'] ? Carbon::parse($o['first']) : null;
            $last = $o['last'] ? Carbon::parse($o['last']) : null;
            $recency = $last ? (int) $last->startOfDay()->diffInDays(now()->startOfDay()) : null;
            $tenure = $first ? (int) $first->startOfDay()->diffInDays(now()->startOfDay()) : (int) Carbon::parse($u->created_at)->startOfDay()->diffInDays(now()->startOfDay());
            $aov = $o['total'] > 0 ? round($billed / $o['total'], 2) : 0.0;
            $freq = $tenure > 0 ? round($o['total'] / max(1, $tenure / 30), 2) : (float) $o['total'];
            $gap = ($o['total'] > 1 && $first && $last) ? round($first->diffInDays($last) / ($o['total'] - 1), 1) : null;

            arsort($h['cat']); arsort($h['svc']); arsort($h['sla']);
            $topCat = array_key_first($h['cat']) ?? null;
            $topSvc = array_key_first($h['svc']) ?? null;
            $topSla = array_key_first($h['sla']) ?? null;
            $catTotal = array_sum($h['cat']);
            $slaTotal = array_sum($h['sla']);
            $rushLines = 0;
            foreach ($h['sla'] as $name => $cnt) {
                if (preg_match('/(kilat|express|prioritas|priority|cepat|rush|same)/i', $name)) $rushLines += $cnt;
            }
            $rushShare = $slaTotal > 0 ? (int) round($rushLines / $slaTotal * 100) : 0;
            $shipTotal = $o['pickup'] + $o['deliver'];
            $pickupShare = $shipTotal > 0 ? (int) round($o['pickup'] / $shipTotal * 100) : 0;

            $status = (string) ($u->status ?? 'active');
            $tier = (string) ($u->tier ?? 'regular');
            $rec = $this->recommendTier($paid, $status, $r);
            $segment = $this->segmentOf($o['total'], $paid, $recency, $tenure, $r);
            $audience = $this->audienceOf($u);

            $catMix = [];
            foreach ($h['cat'] as $name => $val) {
                $catMix[] = ['name' => $name, 'value' => round($val, 2), 'share' => $catTotal > 0 ? (int) round($val / $catTotal * 100) : 0];
            }
            $catMix = array_slice($catMix, 0, 5);

            $profiles[] = [
                'id' => (int) $u->id,
                'user' => $u,
                'name' => (string) $u->name,
                'email' => (string) $u->email,
                'phone' => (string) ($u->phone ?? ''),
                'wa' => $this->waOf($u),
                'address' => (string) ($u->address ?? ''),
                'address_detail' => (string) ($u->address_detail ?? ''),
                'notes' => (string) ($u->internal_notes ?? ''),
                'tier' => $tier,
                'recommended' => $rec,
                'needs_promote' => $rec !== $tier && $rec !== 'inactive',
                'status' => $status,
                'guest' => (bool) ($u->is_guest ?? false),
                'joined' => Carbon::parse($u->created_at)->format('d M Y'),
                'audience' => $audience,
                'segment' => $segment,
                'orders' => $o['total'],
                'active_orders' => $o['active'],
                'done_orders' => $o['done'],
                'cancel_orders' => $o['cancel'],
                'billed' => round($billed, 2),
                'paid' => round($paid, 2),
                'outstanding' => max(0, round($billed - $paid, 2)),
                'aov' => $aov,
                'lunas_rate' => $invCount > 0 ? (int) round($lunas / $invCount * 100) : 0,
                'first_order' => $first?->format('d M Y'),
                'last_order' => $last?->format('d M Y'),
                'recency' => $recency,
                'tenure' => $tenure,
                'freq' => $freq,
                'gap' => $gap,
                'top_cat' => $topCat,
                'top_svc' => $topSvc,
                'top_sla' => $topSla,
                'rush_share' => $rushShare,
                'pickup_share' => $pickupShare,
                'area' => round($h['area'], 2),
                'lines' => $h['lines'],
                'cat_mix' => $catMix,
                'cur' => round((float) ($t->cur ?? 0), 2),
                'prev' => round((float) ($t->prev ?? 0), 2),
                'is_active_30' => $recency !== null && $recency <= $r['active'],
            ];
        }

        return $profiles;
    }

    /* ================= AKSI ================= */

    public function openDetail($id): void { $this->detailId = (int) $id; }
    public function closeDetail(): void { $this->detailId = null; }

    public function openEdit($id): void
    {
        $u = User::where('role', 'customer')->find($id);
        if (!$u) return;
        $this->editId = (int) $u->id;
        $this->eName = (string) $u->name;
        $this->eEmail = (string) $u->email;
        $this->ePhone = (string) ($u->phone ?? '');
        $this->eTier = (string) ($u->tier ?? 'regular');
        $this->eStatus = (string) ($u->status ?? 'active');
        $this->eAddress = (string) ($u->address ?? '');
        $this->eAddressDetail = (string) ($u->address_detail ?? '');
        $this->eNotes = (string) ($u->internal_notes ?? '');
        $this->resetErrorBag();
    }

    public function closeEdit(): void { $this->editId = null; $this->resetErrorBag(); }

    public function saveEdit(): void
    {
        $u = User::where('role', 'customer')->find($this->editId);
        if (!$u) { $this->closeEdit(); return; }

        $data = $this->validate([
            'eName' => 'required|string|min:3|max:255',
            'eEmail' => ['required','email:rfc','max:255', Rule::unique('users','email')->ignore($u->id)],
            'ePhone' => ['nullable','string','max:30','regex:/^[0-9+\-\s().]+$/'],
            'eTier' => 'required|in:regular,silver,gold,vip,inactive',
            'eStatus' => 'required|in:active,suspended,pending',
            'eAddress' => 'nullable|string|max:1000',
            'eAddressDetail' => 'nullable|string|max:500',
            'eNotes' => 'nullable|string|max:1000',
        ], [
            'eName.required' => 'Nama pelanggan wajib diisi.',
            'eName.min' => 'Nama minimal 3 karakter.',
            'eEmail.required' => 'Email wajib diisi.',
            'eEmail.email' => 'Format email tidak valid.',
            'eEmail.unique' => 'Email sudah dipakai akun lain.',
            'ePhone.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
        ]);

        $fill = [
            'name' => trim($data['eName']),
            'email' => trim($data['eEmail']),
            'phone' => trim((string) $data['ePhone']),
            'address' => trim((string) $data['eAddress']),
        ];
        if (in_array('tier', $this->userCols, true)) $fill['tier'] = $data['eTier'];
        if (in_array('status', $this->userCols, true)) $fill['status'] = $data['eStatus'];
        if (in_array('address_detail', $this->userCols, true)) $fill['address_detail'] = trim((string) $data['eAddressDetail']);
        if (in_array('internal_notes', $this->userCols, true)) $fill['internal_notes'] = trim((string) $data['eNotes']);

        $u->forceFill($fill)->save();

        $this->closeEdit();
        session()->flash('crm_success', 'Data pelanggan ' . $u->name . ' diperbarui.');
    }

    public function promote($id): void
    {
        $u = User::where('role', 'customer')->find($id);
        if (!$u || !in_array('tier', $this->userCols, true)) return;

        $profiles = $this->buildProfiles();
        $p = collect($profiles)->firstWhere('id', (int) $id);
        if (!$p) return;

        $u->forceFill(['tier' => $p['recommended']])->save();
        session()->flash('crm_success', 'Tier ' . $u->name . ' dinaikkan menjadi ' . strtoupper($p['recommended']) . ' sesuai nilai transaksinya.');
    }

    public function toggleStatus($id): void
    {
        $u = User::where('role', 'customer')->find($id);
        if (!$u || !in_array('status', $this->userCols, true)) return;
        $next = ((string) $u->status) === 'suspended' ? 'active' : 'suspended';
        $u->forceFill(['status' => $next])->save();
        session()->flash('crm_success', 'Status ' . $u->name . ' menjadi ' . ($next === 'active' ? 'aktif' : 'non-aktif') . '. Riwayat transaksi tetap aman.');
    }

    public function openCreate(): void
    {
        $this->createOpen = true;
        $this->cName = ''; $this->cEmail = ''; $this->cPhone = ''; $this->cTier = 'regular'; $this->cAddress = '';
        $this->resetErrorBag();
    }

    public function closeCreate(): void { $this->createOpen = false; $this->resetErrorBag(); }

    public function saveCreate(): void
    {
        $data = $this->validate([
            'cName' => 'required|string|min:3|max:255',
            'cEmail' => 'required|email:rfc|max:255|unique:users,email',
            'cPhone' => ['nullable','string','max:30','regex:/^[0-9+\-\s().]+$/'],
            'cTier' => 'required|in:regular,silver,gold,vip',
            'cAddress' => 'nullable|string|max:1000',
        ], [
            'cName.required' => 'Nama pelanggan wajib diisi.',
            'cEmail.required' => 'Email wajib diisi.',
            'cEmail.unique' => 'Email sudah terdaftar.',
            'cPhone.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
        ]);

        $fill = [
            'name' => trim($data['cName']),
            'email' => trim($data['cEmail']),
            'password' => Hash::make(Str::random(24)),
            'role' => 'customer',
            'phone' => trim((string) $data['cPhone']),
            'address' => trim((string) $data['cAddress']),
            'status' => 'active',
        ];
        if (in_array('tier', $this->userCols, true)) $fill['tier'] = $data['cTier'];
        if (in_array('is_guest', $this->userCols, true)) $fill['is_guest'] = false;
        if (in_array('internal_notes', $this->userCols, true)) {
            $fill['internal_notes'] = 'Didaftarkan manual oleh ' . (auth()->user()->name ?? 'Owner') . ' pada ' . now()->format('d M Y H:i');
        }

        $u = new User();
        $u->forceFill($fill)->save();

        $this->closeCreate();
        session()->flash('crm_success', 'Pelanggan ' . $u->name . ' ditambahkan. Kata sandi awal dibuat acak — minta pelanggan mengatur ulang saat login pertama.');
    }

    public function clearFilters(): void
    {
        $this->search = ''; $this->tierFilter = 'all'; $this->segmentFilter = 'all';
        $this->audienceFilter = 'all'; $this->statusFilter = 'all'; $this->sort = 'value';
        $this->resetPage();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingTierFilter(): void { $this->resetPage(); }
    public function updatingSegmentFilter(): void { $this->resetPage(); }
    public function updatingAudienceFilter(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }

    /* ================= EKSPOR ================= */

    private function exportRows(array $profiles): array
    {
        $term = mb_strtolower(trim($this->search));
        return array_values(array_filter($profiles, function ($p) use ($term) {
            if ($this->tierFilter !== 'all' && $p['tier'] !== $this->tierFilter) return false;
            if ($this->segmentFilter !== 'all' && $p['segment'] !== $this->segmentFilter) return false;
            if ($this->audienceFilter !== 'all' && $p['audience'] !== $this->audienceFilter) return false;
            if ($this->statusFilter === 'active' && $p['status'] !== 'active') return false;
            if ($this->statusFilter === 'suspended' && $p['status'] !== 'suspended') return false;
            if ($this->statusFilter === 'dormant' && !in_array($p['segment'], ['pasif','hilang'], true)) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($p['name']), $term)
                || str_contains(mb_strtolower($p['email']), $term)
                || str_contains(mb_strtolower($p['phone']), $term);
        }));
    }

    private function exportHead(): array
    {
        return ['Nama','Email','Telepon','Tier','Tier Rekomendasi','Status','Segmen','Tipe','Total Order','Order Selesai','Total Tagihan','Total Dibayar','Sisa Tagihan','Rata-rata Order','Order/Bulan','Order Terakhir','Recency (hari)','Divisi Dominan','Layanan Favorit','SLA Favorit','Porsi Kilat %','Ambil di Toko %','Bergabung'];
    }

    private function exportLine(array $p): array
    {
        return [
            $p['name'], $p['email'], $p['phone'], strtoupper($p['tier']), strtoupper($p['recommended']),
            $p['status'], $p['segment'], $p['audience'] === 'business' ? 'Bisnis' : 'Perorangan',
            $p['orders'], $p['done_orders'], $p['billed'], $p['paid'], $p['outstanding'], $p['aov'], $p['freq'],
            $p['last_order'] ?? '-', $p['recency'] ?? '-', $p['top_cat'] ?? '-', $p['top_svc'] ?? '-', $p['top_sla'] ?? '-',
            $p['rush_share'], $p['pickup_share'], $p['joined'],
        ];
    }

    public function exportExcel()
    {
        $rows = $this->exportRows($this->buildProfiles());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0A3F33" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="n"><NumberFormat ss:Format="#,##0.00"/></Style></Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="CRM Pelanggan"><Table>' . "\n";
        $xml .= '<Row>';
        foreach ($this->exportHead() as $h) $xml .= '<Cell ss:StyleID="h"><Data ss:Type="String">' . htmlspecialchars($h, ENT_XML1) . '</Data></Cell>';
        $xml .= '</Row>' . "\n";

        foreach ($rows as $p) {
            $xml .= '<Row>';
            foreach ($this->exportLine($p) as $v) {
                $isNum = is_int($v) || is_float($v);
                $xml .= '<Cell' . ($isNum ? ' ss:StyleID="n"' : '') . '><Data ss:Type="' . ($isNum ? 'Number' : 'String') . '">'
                      . htmlspecialchars((string) $v, ENT_XML1) . '</Data></Cell>';
            }
            $xml .= '</Row>' . "\n";
        }
        $xml .= '</Table></Worksheet></Workbook>';

        return response()->streamDownload(function () use ($xml) { echo $xml; }, 'crm-pelanggan-' . now()->format('Ymd-His') . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportCsv()
    {
        $rows = $this->exportRows($this->buildProfiles());
        $head = $this->exportHead();

        return response()->streamDownload(function () use ($rows, $head) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $head, ';');
            foreach ($rows as $p) fputcsv($out, $this->exportLine($p), ';');
            fclose($out);
        }, 'crm-pelanggan-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $profiles = $this->buildProfiles();
        $filtered = $this->exportRows($profiles);

        usort($filtered, function ($a, $b) {
            if ($this->sort === 'orders') return $b['orders'] <=> $a['orders'];
            if ($this->sort === 'recent') return [$a['recency'] ?? 99999, $a['name']] <=> [$b['recency'] ?? 99999, $b['name']];
            if ($this->sort === 'name') return strcmp($a['name'], $b['name']);
            if ($this->sort === 'growth') return ($b['cur'] - $b['prev']) <=> ($a['cur'] - $a['prev']);
            return $b['paid'] <=> $a['paid'];
        });

        $total = count($filtered);
        $perPage = 10;
        $page = max(1, $this->getPage());
        $rows = array_slice($filtered, ($page - 1) * $perPage, $perPage);
        $lastPage = max(1, (int) ceil($total / $perPage));

        /* Statistik & profil pasar */
        $r = $this->rules();
        $sumPaid = 0.0; $sumOut = 0.0; $sumCur = 0.0; $sumPrev = 0.0;
        $active30 = 0; $business = 0; $guests = 0; $needPromote = 0; $dormant = 0; $withOrders = 0;
        $tierDist = []; $segDist = []; $catDist = []; $audDist = ['business' => 0, 'personal' => 0];

        foreach ($profiles as $p) {
            $sumPaid += $p['paid']; $sumOut += $p['outstanding'];
            $sumCur += $p['cur']; $sumPrev += $p['prev'];
            if ($p['is_active_30']) $active30++;
            if ($p['audience'] === 'business') $business++;
            if ($p['guest']) $guests++;
            if ($p['needs_promote']) $needPromote++;
            if (in_array($p['segment'], ['pasif','hilang'], true) && $p['paid'] > 0) $dormant++;
            if ($p['orders'] > 0) $withOrders++;
            $tierDist[$p['tier']] = ($tierDist[$p['tier']] ?? 0) + 1;
            $segDist[$p['segment']] = ($segDist[$p['segment']] ?? 0) + 1;
            $audDist[$p['audience']]++;
            foreach ($p['cat_mix'] as $cm) $catDist[$cm['name']] = ($catDist[$cm['name']] ?? 0) + $cm['value'];
        }

        $count = count($profiles);
        $growth = $sumPrev > 0 ? round(($sumCur - $sumPrev) / $sumPrev * 100, 1) : ($sumCur > 0 ? 100.0 : 0.0);

        arsort($catDist); arsort($segDist); arsort($tierDist);
        $topCatName = array_key_first($catDist);
        $topSegName = array_key_first($segDist);
        $topTierName = array_key_first($tierDist);
        $audMajor = $audDist['business'] >= $audDist['personal'] ? 'business' : 'personal';
        $audShare = $count > 0 ? (int) round($audDist[$audMajor] / $count * 100) : 0;
        $catTotal = array_sum($catDist);
        $topCatShare = ($catTotal > 0 && $topCatName) ? (int) round($catDist[$topCatName] / $catTotal * 100) : 0;

        $dist = function (array $arr, int $denom) {
            $out = [];
            $max = $arr ? max($arr) : 0;
            foreach ($arr as $k => $v) {
                $out[] = ['key' => $k, 'count' => $v, 'share' => $denom > 0 ? (int) round($v / $denom * 100) : 0, 'bar' => $max > 0 ? max(5, (int) round($v / $max * 100)) : 5];
            }
            return $out;
        };

        $catDenom = $catTotal;
        $catBars = [];
        $ci = 0;
        $hues = ['#c8ec3f', '#38bdf8', '#fbbf24', '#f472b6', '#34d399', '#a78bfa', '#fb7185', '#22d3ee'];
        foreach ($catDist as $name => $val) {
            $catBars[] = ['name' => $name, 'value' => round($val, 2), 'share' => $catDenom > 0 ? (int) round($val / $catDenom * 100) : 0, 'hue' => $hues[$ci % 8], 'bar' => $catDenom > 0 ? max(4, (int) round($val / $catDenom * 100)) : 4];
            $ci++;
            if ($ci >= 8) break;
        }

        /* Kesesuaian target */
        $fitCat = null; $fitAud = null;
        if ($this->targetCategory !== 'all') {
            $targetName = (string) (Category::where('id', (int) $this->targetCategory)->value('name') ?? '');
            $match = 0; $revMatch = 0.0;
            foreach ($profiles as $p) {
                if ($p['top_cat'] === $targetName && $targetName !== '') { $match++; $revMatch += $p['paid']; }
            }
            $fitCat = [
                'name' => $targetName,
                'customers' => $count > 0 ? (int) round($match / $count * 100) : 0,
                'revenue' => $sumPaid > 0 ? (int) round($revMatch / $sumPaid * 100) : 0,
                'match' => $match,
            ];
        }
        if ($this->targetAudience !== 'all') {
            $match = $audDist[$this->targetAudience] ?? 0;
            $revMatch = 0.0;
            foreach ($profiles as $p) if ($p['audience'] === $this->targetAudience) $revMatch += $p['paid'];
            $fitAud = [
                'name' => $this->targetAudience === 'business' ? 'Bisnis / Organisasi' : 'Perorangan',
                'customers' => $count > 0 ? (int) round($match / $count * 100) : 0,
                'revenue' => $sumPaid > 0 ? (int) round($revMatch / $sumPaid * 100) : 0,
                'match' => $match,
            ];
        }

        $fitScore = null;
        if ($fitCat && $fitAud) $fitScore = (int) round(($fitCat['revenue'] + $fitAud['revenue']) / 2);
        elseif ($fitCat) $fitScore = $fitCat['revenue'];
        elseif ($fitAud) $fitScore = $fitAud['revenue'];

        $leaders = $profiles;
        usort($leaders, fn($a, $b) => $b['paid'] <=> $a['paid']);
        $leaders = array_slice(array_filter($leaders, fn($p) => $p['paid'] > 0), 0, 5);
        $leadMax = $leaders ? max(array_column($leaders, 'paid')) : 0;
        foreach ($leaders as $i => $l) $leaders[$i]['bar'] = $leadMax > 0 ? max(5, (int) round($l['paid'] / $leadMax * 100)) : 5;

        $winback = array_values(array_filter($profiles, fn($p) => in_array($p['segment'], ['pasif','hilang'], true) && $p['paid'] > 0));
        usort($winback, fn($a, $b) => $b['paid'] <=> $a['paid']);
        $winback = array_slice($winback, 0, 5);

        $detail = null;
        if ($this->detailId) {
            $detail = collect($profiles)->firstWhere('id', $this->detailId);
            if ($detail) {
                $recent = Order::with(['invoice', 'items.service'])->where('user_id', $this->detailId)->latest()->limit(8)->get();
                $detail['recent'] = $recent->map(fn($o) => [
                    'id' => (int) $o->id,
                    'invoice' => (string) $o->invoice_number,
                    'status' => (string) $o->status,
                    'date' => $o->created_at?->format('d M Y'),
                    'service' => (string) ($o->items->first()?->service?->name ?? '—'),
                    'items' => $o->items->count(),
                    'total' => (float) ($o->invoice->total_amount ?? 0),
                    'paid' => (float) ($o->invoice->total_paid ?? 0),
                ])->all();
            }
        }

        return view('livewire.admin.customers', [
            'rows' => $rows,
            'page' => $page,
            'lastPage' => $lastPage,
            'total' => $total,
            'detail' => $detail,
            'leaders' => $leaders,
            'winback' => $winback,
            'catBars' => $catBars,
            'tierBars' => $dist($tierDist, $count),
            'segBars' => $dist($segDist, $count),
            'categories' => Category::orderBy('name')->get(['id','name'])->map(fn($c) => ['id' => $c->id, 'name' => (string) $c->name])->all(),
            'fitCat' => $fitCat,
            'fitAud' => $fitAud,
            'fitScore' => $fitScore,
            'market' => [
                'count' => $count,
                'active30' => $active30,
                'with_orders' => $withOrders,
                'business' => $business,
                'personal' => $count - $business,
                'guests' => $guests,
                'need_promote' => $needPromote,
                'dormant' => $dormant,
                'paid' => round($sumPaid, 2),
                'outstanding' => round($sumOut, 2),
                'cur' => round($sumCur, 2),
                'prev' => round($sumPrev, 2),
                'growth' => $growth,
                'aud_major' => $audMajor,
                'aud_share' => $audShare,
                'top_cat' => $topCatName,
                'top_cat_share' => $topCatShare,
                'top_seg' => $topSegName,
                'top_tier' => $topTierName,
                'aov' => $withOrders > 0 ? round($sumPaid / max(1, $withOrders), 2) : 0,
            ],
            'hasFilter' => $this->search !== '' || $this->tierFilter !== 'all' || $this->segmentFilter !== 'all'
                || $this->audienceFilter !== 'all' || $this->statusFilter !== 'all',
        ]);
    }
}