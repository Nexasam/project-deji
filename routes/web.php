<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/properties', function () {
    return view('properties');
});

Route::get('/calendar', function () {
    return view('calendar');
});

Route::get('/property/detail', function () {
    return view('property-detail');
});

Route::get('/property/finance', function () {
    return view('property-finance');
});

Route::get('/property/add/step1', function () {
    return view('property-add-step1');
});

Route::get('/property/add/step1-flat', function () {
    return view('property-add-step1-flat');
});

Route::get('/property/add/step2', function () {
    return view('property-add-step2');
});

Route::get('/property/add/step3', function () {
    return view('property-add-step3');
});

Route::get('/property/add/step4', function () {
    return view('property-add-step4');
});

Route::get('/property/add/step5', function () {
    return view('property-add-step5');
});

Route::get('/property/add/step6', function () {
    return view('property-add-step6');
});

Route::get('/property/add/step7', function () {
    return view('property-add-step7');
});

Route::get('/property/add/step8', function () {
    return view('property-add-step8');
});

Route::get('/property/add/step9', function () {
    return view('property-add-step9');
});

Route::get('/property/add/success', function () {
    return view('property-add-success');
});
