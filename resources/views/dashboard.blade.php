@extends('layouts.dashboard')

@section('title', 'Dashboard - Verified Shortlet')

@section('content')
<div class="p-6 space-y-5">
    {{-- Business Health Banner --}}
    <x-dashboard.business-health-banner 
        :score="87"
        status="EXCELLENT"
        greeting="Good morning, Samson"
        message="Revenue and occupancy are both trending up this month. 2 properties need attention — including an overdue maintenance flag at Bluewater Suite 4B."
        date="Tuesday, 4 August."
    />

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-dashboard.stat-card 
            label="Total earnings"
            value="₦2.84M"
            trend="↑ 12.4% vs last month"
        >
            <x-slot:icon>
                <svg class="w-5 h-5 text-[#FF5A00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot:icon>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            label="Active listings"
            value="6"
            subtext="5 verified, 1 pending"
        >
            <x-slot:icon>
                <svg class="w-5 h-5 text-[#FF5A00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </x-slot:icon>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            label="Occupancy rate"
            value="71%"
            trend="↓ 3.1% vs last month"
        >
            <x-slot:icon>
                <svg class="w-5 h-5 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z" opacity="0.5"/>
                    <path d="M3 3h8v8H3V3z"/>
                </svg>
            </x-slot:icon>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            label="Avg. guest rating"
            value="4.85"
            trend="↑ 0.1 vs last month"
        >
            <x-slot:icon>
                <svg class="w-5 h-5 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </x-slot:icon>
        </x-dashboard.stat-card>
    </div>

    {{-- Main Dashboard Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        {{-- Financial Snapshot (60%) --}}
        <div class="lg:col-span-3">
            <x-dashboard.financial-snapshot />
        </div>

        {{-- AI Host Insight (40%) --}}
        <div class="lg:col-span-2">
            <x-dashboard.ai-host-insight />
        </div>
    </div>

    {{-- Bottom Dashboard Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Quick Actions --}}
        <x-dashboard.quick-actions />

        {{-- Upcoming Activities --}}
        <x-dashboard.upcoming-activities 
            :activities="[
                ['day' => '14', 'month' => 'AUG', 'property' => 'Ikoyi Skyline Loft', 'guests' => '2 guests', 'nights' => '3 nights'],
                ['day' => '14', 'month' => 'AUG', 'property' => 'Ikoyi Skyline Loft', 'guests' => '2 guests', 'nights' => '3 nights'],
                ['day' => '14', 'month' => 'AUG', 'property' => 'Ikoyi Skyline Loft', 'guests' => '2 guests', 'nights' => '3 nights']
            ]"
        />
    </div>
</div>
@endsection
