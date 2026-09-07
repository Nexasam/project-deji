@extends('layouts.dashboard')
@section('active', 'operations')
@section('content')
<div class="p-8"><h1 class="text-2xl font-bold">Operations</h1><p class="mt-2 text-gray-600">Operational tasks for {{ $activeBusiness->name }} will appear here.</p></div>
@endsection
