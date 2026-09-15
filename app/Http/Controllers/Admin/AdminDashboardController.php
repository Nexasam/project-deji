<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Platform\AdminDashboardSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request, AdminDashboardSummary $summary): View
    {
        return view('admin.dashboard', ['summary' => $summary->for($request->user())]);
    }
}
