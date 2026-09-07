<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Review {{ $property->name }} - Verified Shortlet</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-gray-100 font-sans antialiased"><div class="min-h-screen flex flex-col">
<header class="bg-white border-b border-gray-200"><div class="max-w-4xl mx-auto px-6 py-3 flex items-center justify-between"><div class="flex items-center gap-2"><img src="/logo1.png" alt="VS Logo" class="h-7"><span class="text-sm font-semibold">Verified Shortlet</span></div><div class="flex items-center gap-3"><span class="text-xs text-gray-500">Step 10 of 10</span><a href="{{ route('owner.properties.index') }}" class="px-3 py-1.5 text-xs border rounded-lg">Save & exit</a></div></div><div class="h-0.5 bg-[#FF5A00]"></div></header>
<main class="flex-1 px-4 py-7"><div class="w-full max-w-4xl mx-auto"><div class="mb-5"><p class="text-[#FF5A00] text-xs font-bold uppercase tracking-wider">Last look</p><h1 class="text-2xl font-bold mt-1">Review your listing</h1><p class="text-gray-500 text-sm mt-1">Everything below comes from your saved property draft.</p></div>
@if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
@php $sections=[
 [2,'Location',[data_get($property->address,'city').' · '.data_get($property->address,'state'),data_get($property->address,'line_1')]],
 [3,'The basics',[str($property->property_type)->replace('-',' ')->title().' · '.str($property->booking_mode)->title(),$property->capacity.' Guests · '.$property->bedrooms.' bed · '.$property->bathrooms.' bath']],
 [4,'Amenities',[$property->amenities->count().' amenities selected']],
 [5,'Photos & video',[$property->media->where('media_type','image')->count().' photos · '.$property->media->where('media_type','video')->count().' video']],
 [6,'Assets',[$property->assets->pluck('name')->join(', ') ?: 'None added']],
 [7,'Documents',[$property->documents->count().' file(s) attached']],
 [8,'Name & price',[$property->name,'₦'.number_format((float)$property->default_nightly_price).' / night']],
 [9,'Booking channels',[$property->channelConnections->count().' connection request(s) pending']],
]; @endphp
<div class="space-y-4 mb-6">@foreach($sections as [$step,$title,$lines])<section class="bg-white rounded-xl border border-gray-200 p-6"><div class="flex justify-between mb-3"><h2 class="font-bold">{{ $title }}</h2><a href="{{ route('owner.properties.wizard.step',['property'=>$property,'step'=>$step]) }}" class="text-[#FF5A00] text-sm font-semibold">Edit</a></div>@foreach($lines as $line)<p class="text-sm text-gray-600">{{ $line }}</p>@endforeach @if($step===5)<div class="flex gap-2 mt-3 flex-wrap">@foreach($property->media->where('media_type','image') as $image)<img src="{{ Storage::disk($image->storage_disk)->url($image->storage_path) }}" class="w-16 h-16 rounded-lg object-cover" alt="{{ $image->alt_text }}">@endforeach</div>@endif</section>@endforeach</div>
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm text-gray-700">Submitting sends this listing for verification. It will remain pending until reviewed.</div>
<div class="flex justify-between items-center"><a href="{{ route('owner.properties.wizard.step',['property'=>$property,'step'=>9]) }}" class="text-sm underline text-gray-500">Back</a><form method="POST" action="{{ route('owner.properties.wizard.submit',$property) }}">@csrf<button class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-2.5 px-8 rounded-lg text-sm">Submit for review</button></form></div>
</div></main></div></body></html>
