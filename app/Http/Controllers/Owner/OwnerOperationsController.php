<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OwnerOperationsController extends Controller
{
    public function __invoke(): View
    {
        return view('owner.operations');
    }
}
