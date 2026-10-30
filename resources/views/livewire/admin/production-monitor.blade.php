<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">📡 Monitor Produksi & Laporan Delay</h2>
        <p class="text-sm text-gray-500">Hanya pesanan bermasalah yang tampil (management by exception). Khusus Owner.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-red-500"><p class="text-[10px] uppercase font-bold text-gray-500">Terlambat</p><p class="text-xl font-bold text-red-600">{{ $stats['delayed'] }}</p></div>
        <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-yellow-500"><p class="text-[10px] uppercase font-bold text-gray-500">Berisiko (≤24j)</p><p class="text-xl font-bold text-yellow-600">{{ $stats['at_risk'] }}</p></div>
        <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-orange-500"><p class="text-[10px] uppercase font-bold text-gray-500">Macet per Tahap</p><p class="text-xl font-bold text-orange-600">{{ $stats['stalled'] }}</p></div>
        <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-purple-500"><p class="text-[10px] uppercase font-bold text-gray-500">Kendala Terbuka</p><p class="text-xl font-bold text-purple-600">{{ $stats['incidents'] }}</p></div>
    </div>

    @forelse($orders as $order)
        @php
            $t = $order->trace;
            $stateMeta = [
                'delayed' => ['bg-red-100 text-red-700', 'TERLAMBAT'],
                'at_risk' => ['bg-yellow-100 text-yellow-700', 'BERISIKO'],
                'stalled' => ['bg-orange-100 text-orange-700', 'MACET DI TAHAP INI'],
            ][$t['state']] ?? ['bg-gray-100 text-gray-600', strtoupper($t['state'])];
            $openImpacts = $order->incidents->where('status','open')->sum('impact_days');
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-5 overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex flex-col md:flex-row justify-between md:items-center gap-2">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-bold text-gray-900">{{ $order->invoice_number }}</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $stateMeta[0] }}">{{ $stateMeta[1] }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">TAHAP: {{ strtoupper(str_replace('_',' ', $t['stage'])) }}</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $order->user->name }} • di tahap ini sejak {{ $t['stage_since']?->format('d M Y, H:i') }}
                        (sudah <strong>{{ $this->formatDurasi($t['dwell_hours']) }}</strong>@if($t['budget_hours']) / anggaran {{ $this->formatDurasi($t['budget_hours']) }}@endif)
                    </p>
                </div>
                <div class="text-xs text-gray-600 text-right">
                    <p>Deadline: <strong>{{ $t['deadline']?->format('d M Y') ?? '-' }}</strong>
                        @if($t['hours_left'] !== null)
                            @if($t['hours_left'] > 0)
                                (sisa <strong>{{ $this->formatDurasi($t['hours_left']) }}</strong>)
                            @else
                                (<span class="text-red-600 font-bold">LEWAT {{ $this->formatDurasi(abs($t['hours_left'])) }}</span>)
                            @endif
                        @endif
                    </p>
                </div>
            </div>

            <div class="p-5 space-y-4">
                {{-- Kendala --}}
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase mb-2">Kendala Tercatat</h4>
                    @forelse($order->incidents->sortByDesc('id') as $inc)
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 p-2.5 rounded-lg border mb-2 {{ $inc->status === 'open' ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-gray-200' }}">
                            <div class="text-xs">
                                <span class="font-bold {{ $inc->status === 'open' ? 'text-orange-700' : 'text-gray-500' }}">{{ $inc->type_label }}</span>
                                <span class="text-gray-500"> +{{ $inc->impact_days }} hari • {{ $inc->reporter->name ?? '-' }} • {{ $inc->created_at->format('d M H:i') }}</span>
                                <p class="text-gray-600 mt-0.5">{{ $inc->description }}</p>
                            </div>
                            @if($inc->status === 'open')
                                <button wire:click="resolveIncident({{ $inc->id }})" wire:confirm="Tandai kendala ini SELESAI?" class="px-3 py-1.5 text-xs font-semibold bg-green-600 text-white rounded hover:bg-green-700">✓ Selesai</button>
                            @else
                                <span class="text-[10px] text-gray-400">Selesai {{ $inc->resolved_at?->format('d M H:i') }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Tidak ada kendala tercatat — delay terdeteksi otomatis oleh sistem.</p>
                    @endforelse
                </div>

                {{-- Revisi deadline (hak Owner) --}}
                <div class="pt-3 border-t border-gray-100 flex flex-col md:flex-row md:items-center gap-2">
                    @if($editId === $order->id)
                        <input type="date" wire:model="editValue" class="bg-white border border-gray-300 text-sm rounded-lg p-2">
                        <button wire:click="saveDeadline({{ $order->id }})" class="px-3 py-2 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Revisi</button>
                        <button wire:click="$set('editId', null)" class="px-3 py-2 text-xs text-gray-500">Batal</button>
                    @else
                        <button wire:click="startEdit({{ $order->id }})" class="px-3 py-2 text-xs font-semibold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">✏ Revisi Deadline (Owner)</button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
            <p class="text-gray-500 font-medium">Tidak ada delay, risiko, maupun kendala terbuka. Produksi sehat ✅</p>
        </div>
    @endforelse
</div>