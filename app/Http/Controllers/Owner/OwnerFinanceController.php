<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OwnerFinanceController extends Controller
{
    public function __invoke(): View
    {
        return view('property-finance');
    }
}
