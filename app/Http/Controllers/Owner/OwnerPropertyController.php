<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OwnerPropertyController extends Controller
{
    public function index(): View
    {
        return view('properties');
    }

    public function create(): View
    {
        return view('property-add-step1');
    }
}
