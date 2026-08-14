<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OwnerCalendarController extends Controller
{
    public function __invoke(): View
    {
        return view('calendar');
    }
}
