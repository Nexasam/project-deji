<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\ActiveBusinessContext;
use Illuminate\View\View;

class OwnerFinanceController extends Controller
{
    public function __invoke(ActiveBusinessContext $context): View
    {
        $business = $context->business;

        // ── KPI cards ──────────────────────────────────────────────────────────
        $kpis = [
            ['label' => 'Revenue',        'value' => '₦5,150,000', 'change' => '+8%',  'change_label' => 'vs last 30d', 'positive' => true],
            ['label' => 'Expenses',       'value' => '₦845,000',   'change' => '-3%',  'change_label' => 'vs last 30d', 'positive' => true],
            ['label' => 'Net Profit',     'value' => '₦4,305,000', 'change' => '+9%',  'change_label' => 'vs last 30d', 'positive' => true],
            ['label' => 'Occupancy',      'value' => '78%',         'change' => '-2%',  'change_label' => 'vs last 30d', 'positive' => false],
            ['label' => 'Pending Payout', 'value' => '₦1,240,000', 'change' => 'Scheduled 28 Aug', 'change_label' => '', 'positive' => null],
            ['label' => 'Commission',     'value' => '₦257,500',   'change' => '5% platform fee', 'change_label' => '', 'positive' => null],
        ];

        // ── Bar chart data (Revenue vs Expenses, last 6 months) ───────────────
        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $chartRevenue  = [1200000, 1550000, 1400000, 1800000, 2100000, 2300000];
        $chartExpenses = [210000,  280000,  195000,  310000,  270000,  340000];
        $chartMax = max(array_merge($chartRevenue, $chartExpenses));

        // ── Revenue by property ────────────────────────────────────────────────
        $revenueByProperty = [
            ['name' => 'Bluewater Suite 2A',       'amount' => '₦1,980,000', 'pct' => 38],
            ['name' => 'Lekki Waterview Suites',   'amount' => '₦1,920,000', 'pct' => 37],
            ['name' => 'Palm Court 1',              'amount' => '₦1,250,000', 'pct' => 25],
        ];

        // ── Expenses by category ───────────────────────────────────────────────
        $expensesByCategory = [
            ['name' => 'Commission & Fees', 'amount' => '₦1,010,000', 'pct' => 60],
            ['name' => 'Cleaning',          'amount' => '₦1,010,000', 'pct' => 45],
            ['name' => 'Maintenance',       'amount' => '₦1,008,000', 'pct' => 30],
        ];

        // ── Transactions ───────────────────────────────────────────────────────
        $transactions = [
            ['date' => '2 Jun 2026', 'property' => 'Bluewater Suite 2A',     'type' => 'Revenue',    'description' => 'Booking #BK-0041',        'status' => 'Settled',  'amount' => '+₦185,000',  'positive' => true],
            ['date' => '4 Jun 2026', 'property' => 'Lekki Waterview Suites', 'type' => 'Expense',    'description' => 'Cleaning fee',            'status' => 'Settled',  'amount' => '-₦12,500',   'positive' => false],
            ['date' => '7 Jun 2026', 'property' => 'Palm Court 1',           'type' => 'Revenue',    'description' => 'Booking #BK-0042',        'status' => 'Settled',  'amount' => '+₦220,000',  'positive' => true],
            ['date' => '9 Jun 2026', 'property' => 'Bluewater Suite 2A',     'type' => 'Commission', 'description' => '5% platform commission',  'status' => 'Settled',  'amount' => '-₦9,250',    'positive' => false],
            ['date' => '12 Jun 2026','property' => 'Lekki Waterview Suites', 'type' => 'Revenue',    'description' => 'Booking #BK-0043',        'status' => 'Pending',  'amount' => '+₦310,000',  'positive' => true],
            ['date' => '15 Jun 2026','property' => 'Palm Court 1',           'type' => 'Payout',     'description' => 'Payout to bank account',  'status' => 'Scheduled','amount' => '-₦680,000',  'positive' => false],
            ['date' => '18 Jun 2026','property' => 'Bluewater Suite 2A',     'type' => 'Revenue',    'description' => 'Booking #BK-0044',        'status' => 'Settled',  'amount' => '+₦195,000',  'positive' => true],
            ['date' => '21 Jun 2026','property' => 'Lekki Waterview Suites', 'type' => 'Expense',    'description' => 'Maintenance – AC repair',  'status' => 'Settled',  'amount' => '-₦45,000',   'positive' => false],
        ];

        view()->share('activeBusiness', $business);

        return view('owner.finance', compact(
            'business', 'kpis', 'chartMonths', 'chartRevenue', 'chartExpenses', 'chartMax',
            'revenueByProperty', 'expensesByCategory', 'transactions'
        ));
    }
}
