<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Finance – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F4] font-sans antialiased" x-data="{ sidebarOpen: false }">
<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('partials.sidebar-nav', ['active' => 'finance'])

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top header --}}
        @include('partials.header')

        {{-- Scrollable content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="p-6 max-w-[1200px] mx-auto space-y-5">

                {{-- Page title + actions --}}
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Finance</h1>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $business->name }}
                            <span class="mx-1 text-gray-300">›</span>
                            Lekki Waterview Suites
                            <span class="mx-1 text-gray-300">›</span>
                            Finance
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button class="fin-btn-outline">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export report
                        </button>
                        <button class="fin-btn-primary">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add expense
                        </button>
                    </div>
                </div>

                {{-- Filter bar --}}
                <div class="fin-card flex items-center gap-2 flex-wrap py-3 px-4">
                    <button class="fin-filter-pill active">
                        <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        All Dates
                        <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="h-4 w-px bg-gray-200"></div>
                    @foreach(['Day','Weekly','Monthly'] as $p)
                        <button class="fin-period-pill {{ $p === 'Monthly' ? 'active' : '' }}">{{ $p }}</button>
                    @endforeach
                    <div class="h-4 w-px bg-gray-200"></div>
                    <button class="fin-filter-pill">
                        Last 30 Days
                        <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <button class="fin-filter-pill">
                        Lekki Waterview Suites
                        <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                {{-- KPI cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3">
                    @foreach($kpis as $kpi)
                    <div class="fin-card px-4 py-3 flex flex-col gap-1 min-w-0">
                        <span class="text-[11px] text-gray-400 font-medium">{{ $kpi['label'] }}</span>
                        <span class="text-[17px] font-extrabold text-gray-900 leading-tight truncate">{{ $kpi['value'] }}</span>
                        @if($kpi['positive'] === true)
                            <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-0.5">
                                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                {{ $kpi['change'] }} <span class="text-gray-400 font-normal ml-0.5">{{ $kpi['change_label'] }}</span>
                            </span>
                        @elseif($kpi['positive'] === false)
                            <span class="text-[11px] font-semibold text-red-500 flex items-center gap-0.5">
                                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                {{ $kpi['change'] }} <span class="text-gray-400 font-normal ml-0.5">{{ $kpi['change_label'] }}</span>
                            </span>
                        @else
                            <span class="text-[11px] text-gray-400">{{ $kpi['change'] }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Revenue vs Expenses chart --}}
                <div class="fin-card p-5">
                    <div class="flex items-start justify-between gap-4 mb-1">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Revenue vs Expenses</h2>
                            <p class="text-[11px] text-gray-400 mt-0.5">Revenue sits on top — expenses are shown as a thinner line underneath so the two never read as equal in size.</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#FF5A00] inline-block"></span> Revenue
                            </span>
                            <span class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                <span class="w-2.5 h-2.5 rounded-sm bg-gray-200 inline-block"></span> Expenses
                            </span>
                        </div>
                    </div>

                    {{-- Chart --}}
                    <div class="mt-4 flex items-end gap-3 h-48 pl-10 relative">

                        {{-- Y-axis labels --}}
                        <div class="absolute left-0 top-0 bottom-0 flex flex-col justify-between text-[10px] text-gray-300 text-right w-9">
                            @php
                                $ySteps = 5;
                                for ($i = $ySteps; $i >= 0; $i--) {
                                    $val = ($chartMax / $ySteps) * $i;
                                    echo '<span>' . ($val >= 1000000 ? '₦' . number_format($val/1000000,1) . 'M' : '₦' . number_format($val/1000) . 'k') . '</span>';
                                }
                            @endphp
                        </div>

                        {{-- Bars --}}
                        @foreach($chartMonths as $i => $month)
                        @php
                            $revPct = round(($chartRevenue[$i] / $chartMax) * 100);
                            $expPct = round(($chartExpenses[$i] / $chartMax) * 100);
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                            <div class="w-full flex items-end justify-center gap-1 flex-1">
                                {{-- Revenue bar --}}
                                <div class="flex-1 bg-[#FF5A00] rounded-t-sm transition-all" style="height:{{ $revPct }}%"></div>
                                {{-- Expense bar --}}
                                <div class="w-[40%] bg-gray-200 rounded-t-sm transition-all" style="height:{{ $expPct }}%"></div>
                            </div>
                            <span class="text-[10px] text-gray-400 mt-1">{{ $month }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Revenue by property + Expenses by category --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    {{-- Revenue by property --}}
                    <div class="fin-card p-5 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-gray-900">Revenue by property</h2>
                            <button class="fin-filter-pill text-[11px]">
                                <svg width="9" height="9" fill="currentColor" viewBox="0 0 20 20"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="11" y="3" width="6" height="6" rx="1"/><rect x="3" y="11" width="6" height="6" rx="1"/><rect x="11" y="11" width="6" height="6" rx="1"/></svg>
                                All Dates
                                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            @foreach($revenueByProperty as $row)
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[13px] font-medium text-gray-700">{{ $row['name'] }}</span>
                                    <span class="text-[13px] font-bold text-gray-900">{{ $row['amount'] }}</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#FF5A00] rounded-full" style="width:{{ $row['pct'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button class="fin-full-report-btn mt-auto">VIEW FULL REPORT</button>
                    </div>

                    {{-- Expenses by category --}}
                    <div class="fin-card p-5 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-gray-900">Expenses by category</h2>
                            <button class="fin-filter-pill text-[11px]">
                                <svg width="9" height="9" fill="currentColor" viewBox="0 0 20 20"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="11" y="3" width="6" height="6" rx="1"/><rect x="3" y="11" width="6" height="6" rx="1"/><rect x="11" y="11" width="6" height="6" rx="1"/></svg>
                                All Dates
                                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            @foreach($expensesByCategory as $row)
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[13px] font-medium text-gray-700">{{ $row['name'] }}</span>
                                    <span class="text-[13px] font-bold text-gray-900">{{ $row['amount'] }}</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gray-900 rounded-full" style="width:{{ $row['pct'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button class="fin-full-report-btn mt-auto">VIEW FULL REPORT</button>
                    </div>
                </div>

                {{-- Transactions --}}
                <div class="fin-card p-5">
                    <div class="flex items-start justify-between gap-4 mb-1 flex-wrap">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Transactions</h2>
                            <p class="text-[11px] text-gray-400 mt-0.5">Every revenue, expense, commission and payout entry</p>
                        </div>
                        <button class="text-[11px] text-[#FF5A00] font-semibold flex items-center gap-1 hover:underline">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export CSV
                        </button>
                    </div>

                    {{-- Search + filter row --}}
                    <div class="flex items-center gap-3 mt-4 mb-5 flex-wrap">
                        <div class="relative flex-1 min-w-[180px]">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" placeholder="Search transactions…"
                                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#FF5A00] focus:border-[#FF5A00]" />
                        </div>
                        <div class="relative">
                            <select class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-200 rounded-lg bg-white text-gray-600 focus:outline-none focus:ring-1 focus:ring-[#FF5A00] cursor-pointer">
                                <option>All types</option>
                                <option>Revenue</option>
                                <option>Expense</option>
                                <option>Commission</option>
                                <option>Payout</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto -mx-5">
                        <table class="w-full text-sm min-w-[640px]">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider pb-2 pl-5">Date</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider pb-2 pl-3">Property</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider pb-2 pl-3">Type</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider pb-2 pl-3">Description</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider pb-2 pl-3">Status</th>
                                    <th class="text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider pb-2 pr-5">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="py-3 pl-5 text-[12px] text-gray-500 whitespace-nowrap">{{ $tx['date'] }}</td>
                                    <td class="py-3 pl-3 text-[12px] text-gray-700 font-medium whitespace-nowrap">{{ $tx['property'] }}</td>
                                    <td class="py-3 pl-3">
                                        @php
                                            $typeColors = [
                                                'Revenue'    => 'bg-emerald-50 text-emerald-700',
                                                'Expense'    => 'bg-red-50 text-red-600',
                                                'Commission' => 'bg-amber-50 text-amber-700',
                                                'Payout'     => 'bg-blue-50 text-blue-600',
                                            ];
                                            $cls = $typeColors[$tx['type']] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $cls }}">
                                            {{ $tx['type'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 pl-3 text-[12px] text-gray-600">{{ $tx['description'] }}</td>
                                    <td class="py-3 pl-3">
                                        @php
                                            $statusColors = [
                                                'Settled'   => 'text-emerald-600',
                                                'Pending'   => 'text-amber-600',
                                                'Scheduled' => 'text-blue-600',
                                            ];
                                            $sc = $statusColors[$tx['status']] ?? 'text-gray-500';
                                        @endphp
                                        <span class="text-[12px] font-medium {{ $sc }}">{{ $tx['status'] }}</span>
                                    </td>
                                    <td class="py-3 pr-5 text-right text-[13px] font-bold whitespace-nowrap {{ $tx['positive'] ? 'text-emerald-600' : 'text-gray-900' }}">
                                        {{ $tx['amount'] }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-sm text-gray-400">No transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="h-4"></div>
            </div>
        </main>
    </div>
</div>

@push('styles')
<style>
.fin-card        { background:#fff; border-radius:12px; border:1px solid #f0f0f0; }
.fin-btn-outline { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:12px; font-weight:600; color:#374151; background:#fff; cursor:pointer; transition:border-color .15s,background .15s; white-space:nowrap; }
.fin-btn-outline:hover { border-color:#9ca3af; background:#f9fafb; }
.fin-btn-primary { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border:none; border-radius:8px; font-size:12px; font-weight:700; color:#fff; background:#FF5A00; cursor:pointer; transition:background .15s; white-space:nowrap; }
.fin-btn-primary:hover { background:#E64F00; }
.fin-filter-pill { display:inline-flex; align-items:center; gap:5px; padding:5px 10px; border:1.5px solid #e5e7eb; border-radius:999px; font-size:12px; font-weight:500; color:#374151; background:#fff; cursor:pointer; white-space:nowrap; transition:border-color .15s; }
.fin-filter-pill:hover { border-color:#d1d5db; }
.fin-filter-pill.active { border-color:#FF5A00; color:#FF5A00; }
.fin-period-pill { padding:5px 12px; border-radius:999px; border:1.5px solid transparent; font-size:12px; font-weight:500; color:#6b7280; background:transparent; cursor:pointer; transition:all .15s; }
.fin-period-pill:hover { background:#f3f4f6; }
.fin-period-pill.active { background:#111; color:#fff; border-color:#111; font-weight:600; }
.fin-full-report-btn { width:100%; padding:10px; border-radius:999px; border:1.5px solid #e5e7eb; background:#fff; font-size:11px; font-weight:700; letter-spacing:.06em; color:#374151; cursor:pointer; transition:border-color .15s,background .15s; }
.fin-full-report-btn:hover { border-color:#d1d5db; background:#f9fafb; }
</style>
@endpush

</body>
</html>
