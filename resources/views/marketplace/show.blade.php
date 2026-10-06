@extends('layouts.app')

@section('content')
<x-navbar />
<main class="min-h-screen bg-[#f3f3f2] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
<div class="mx-auto max-w-[1380px]">
    @php
        $coverMedia = $property->media->firstWhere('is_primary', true) ?? $property->media->firstWhere('media_type', \App\Enums\PropertyMediaType::Image);
        $coverImage = $coverMedia?->external_url ?: ($coverMedia?->storage_path ? '/storage/'.ltrim($coverMedia->storage_path, '/') : '/image.png');
        $blockedIntervals = $property->bookings->map(fn($booking) => ['start' => $booking->arrival_date->toDateString(), 'end' => $booking->departure_date->toDateString()])
            ->concat($property->availabilityBlocks->map(fn($block) => ['start' => $block->starts_on->toDateString(), 'end' => $block->ends_on->toDateString()]))->values();
        $gallery = $property->media->map(fn($media) => ['url' => $media->external_url ?: ($media->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png'), 'type' => $media->media_type->value, 'title' => $media->title ?: $property->name])->values();
        if ($gallery->isEmpty()) $gallery->push(['url' => '/image.png', 'type' => 'image', 'title' => $property->name]);
        $promotions = $property->promotions->filter(fn($promotion) => $promotion->discount_type->value === 'percentage')->map(fn($promotion) => ['minimum_nights' => $promotion->minimum_stay_nights ?? 1, 'percentage' => (float)$promotion->discount_value])->values();
        $hostSource = trim((string) ($property->business?->primary_contact_name ?: $property->owner_name ?: ''));
        $hostFirstName = filled($hostSource) ? Str::of($hostSource)->squish()->explode(' ')->first() : null;
        $hostDisplayName = $hostFirstName ?: 'Host';
        $hostInitials = Str::of($hostDisplayName)->substr(0, 2)->upper();
        $hostingSince = $property->business?->onboarding_completed_at ?: $property->business?->created_at;
        $hostingYears = $hostingSince ? max(1, (int) floor($hostingSince->diffInYears(now()))) : null;
    @endphp
    <div class="flex flex-wrap items-end justify-between gap-4">
	        <div><h1 class="text-3xl font-black text-slate-950">{{ $property->marketplaceListing->public_title }}</h1><p class="mt-2 text-sm text-slate-500">Verified Shortlet &gt; Explore stays &gt; {{ $property->marketplaceListing->public_title }}</p><p class="mt-2 text-sm font-semibold text-slate-700">⌖ {{ collect([data_get($property->address, 'line_1'), data_get($property->address, 'city'), data_get($property->address, 'state')])->filter()->join(', ') }}</p></div>
        <div class="flex gap-2"><button type="button" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black">Share</button>@auth<button type="button" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black">♡ Save</button>@endauth</div>
    </div>
    <div class="mt-6 grid gap-5 lg:grid-cols-[minmax(0,2fr)_minmax(340px,1fr)] lg:items-start" x-data="stayBookingCalendar(@js($blockedIntervals), @js($gallery), {{ (float)$property->default_nightly_price }}, {{ $property->capacity }}, @js($promotions), @js(route('marketplace.checkout.quote', $property->marketplaceListing->slug)))">
        <div class="min-w-0">
            <div class="grid gap-3 md:grid-cols-[1.8fr_1fr]">
                <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-slate-900 shadow-sm md:aspect-auto md:min-h-[430px]">
                    <template x-if="activeMedia.type==='video'"><video controls preload="metadata" :src="activeMedia.url" class="h-full w-full object-contain"></video></template>
                    <template x-if="activeMedia.type==='image'"><img :src="activeMedia.url" x-on:error="$event.target.src='/image.png'" :alt="activeMedia.title" class="h-full w-full object-cover"></template>
                    <button x-show="activeMedia.type==='video'" type="button" class="absolute inset-0 m-auto grid size-16 place-items-center rounded-full bg-orange-600 text-2xl text-white">▶</button>
                </div>
                <div class="hidden grid-rows-2 gap-3 md:grid"><template x-for="(media,index) in gallery.slice(0,4)" :key="media.url+index"><button type="button" @click="activeIndex=index" class="relative min-h-0 overflow-hidden rounded-2xl bg-slate-100"><template x-if="media.type==='image'"><img :src="media.url" :alt="media.title" class="h-full w-full object-cover"></template><template x-if="media.type==='video'"><div class="grid h-full place-items-center bg-slate-800 text-3xl text-white">▶</div></template><span x-show="index===3 && gallery.length>4" class="absolute inset-0 grid place-items-center bg-slate-950/60 text-sm font-black text-white" x-text="'View all '+gallery.length+' photos'"></span></button></template></div>
            </div>
            <div class="mt-3 flex gap-3 overflow-x-auto pb-2 md:hidden"><template x-for="(media,index) in gallery" :key="media.url+index"><button type="button" @click="activeIndex=index" class="relative h-20 w-24 shrink-0 overflow-hidden rounded-xl border-2 bg-slate-100 transition" :class="activeIndex===index?'border-orange-500 ring-2 ring-orange-100':'border-transparent opacity-75'"><img x-show="media.type==='image'" :src="media.url" :alt="media.title" class="h-full w-full object-cover"><span x-show="media.type==='video'" class="grid h-full place-items-center bg-slate-800 text-white">▶</span></button></template></div>

            <dl class="mt-5 grid grid-cols-2 gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-4">
                @foreach([['Guests',$property->capacity,'♙','Max capacity'],['Bedrooms',$property->bedrooms,'▱',Str::plural('bedroom',$property->bedrooms)],['Bathrooms',$property->bathrooms,'◯','Private'],['Check-in',substr($property->marketplaceListing->check_in_time ?: '14:00',0,5),'◷','Arrival time']] as [$label,$value,$icon,$note])
                    <div class="flex items-center gap-3"><span class="text-2xl text-orange-600">{{ $icon }}</span><div><dd class="font-black text-slate-950">{{ $value }} {{ is_numeric($value) && $label !== 'Bathrooms' ? strtolower($label) : '' }}</dd><dt class="text-xs text-slate-500">{{ $note }}</dt></div></div>
                @endforeach
            </dl>

            <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6"><h2 class="text-xl font-black text-slate-950">Amenities</h2><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach($property->amenities->take(9) as $amenity)<p class="text-sm font-semibold text-slate-700"><span class="mr-2 text-orange-600">✓</span>{{ $amenity->name }}</p>@endforeach</div></section>
            <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6"><h2 class="text-xl font-black text-slate-950">Description</h2><p class="mt-4 text-sm leading-7 text-slate-600">{{ $property->marketplaceListing->public_description }}</p></section>
            <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-xl font-black text-slate-950">Availability calendar</h2><p class="mt-1 text-sm text-slate-500">Select check-in and checkout. Booked dates include synchronized iCal reservations.</p></div><label class="text-xs font-bold text-slate-600">Jump to month <input type="month" x-model="jumpMonth" :min="minimumMonth" :max="maximumMonth" @change="jumpToMonth" class="ml-1 text-xs"></label></div>
                <div class="p-5"><div class="mb-5 flex items-center justify-between rounded-xl bg-slate-50 p-2"><button type="button" @click="previous" :disabled="offset===0" class="rounded-lg px-3 py-2 text-sm font-bold disabled:opacity-30">←</button><div class="text-center"><p class="text-sm font-black text-slate-700" x-text="selectionText"></p><p class="text-[11px] text-slate-400">Browse up to 18 months ahead</p></div><button type="button" @click="next" :disabled="offset>=17" class="rounded-lg px-3 py-2 text-sm font-bold disabled:opacity-30">→</button></div>
                    <div x-show="checkIn || checkOut" class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-orange-100 bg-orange-50 px-4 py-3">
                        <p class="text-xs font-bold text-orange-900" x-text="checkIn && checkOut ? 'Selected dates: '+formatDate(checkIn)+' → '+formatDate(checkOut) : 'Check-in selected: '+formatDate(checkIn)+'. Choose checkout or change check-in.'"></p>
                        <div class="flex gap-2">
                            <button type="button" x-show="checkIn && !checkOut" @click="checkIn=''; checkOut=''; selectionError=''; serverQuote=null" class="rounded-lg border border-orange-200 bg-white px-3 py-1.5 text-xs font-black text-orange-700">Change check-in</button>
                            <button type="button" x-show="checkIn && checkOut" @click="checkIn=''; checkOut=''; selectionError=''; serverQuote=null" class="rounded-lg border border-orange-200 bg-white px-3 py-1.5 text-xs font-black text-orange-700">Clear dates</button>
                        </div>
                    </div>
                    <div class="grid gap-8"><template x-for="month in months" :key="month.key"><section class="min-w-0"><h3 class="mb-3 text-center text-sm font-extrabold" x-text="month.label"></h3><div class="grid grid-cols-7 gap-1 text-center"><template x-for="label in ['S','M','T','W','T','F','S']"><span class="py-1 text-[10px] font-bold text-slate-400" x-text="label"></span></template><template x-for="blank in month.offset"><span></span></template><template x-for="day in month.days" :key="day.date"><button type="button" @click="selectDate(day)" :disabled="day.past||day.blocked" class="flex aspect-square min-w-0 items-center justify-center rounded-lg text-xs transition" :class="dayClass(day)" :title="day.blocked?'Booked or unavailable':'Available'" x-text="day.number"></button></template></div></section></template></div>
                    <div class="mt-5 flex flex-wrap gap-4 text-xs font-semibold"><span class="flex items-center gap-2"><i class="size-3 rounded bg-emerald-200 ring-1 ring-emerald-300"></i>Available</span><span class="flex items-center gap-2"><i class="size-3 rounded bg-red-100 ring-1 ring-red-200"></i>Booked</span><span class="flex items-center gap-2"><i class="size-3 rounded bg-orange-500"></i>Your dates</span></div><p x-show="selectionError" x-text="selectionError" class="mt-3 rounded-lg bg-red-50 p-3 text-xs font-semibold text-red-700"></p>
                </div>
            </section>
        </div>
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-24">
            @if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>@endif
	            @if($property->superhost_badge_enabled)<span class="text-xs font-bold text-orange-700">★ Super host</span>@endif
            <p class="mt-3 text-sm text-slate-500">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }}</p>
            <dl class="mt-5 hidden grid-cols-3 gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm sm:max-w-lg">
                <div class="rounded-xl bg-slate-50 px-2.5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="size-5 shrink-0 text-orange-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4M4 11h16M4 11v8M20 11v8M4 16h16M8 11V9.5A1.5 1.5 0 0 1 9.5 8h5A1.5 1.5 0 0 1 16 9.5V11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <dd class="text-2xl font-extrabold leading-none text-slate-950">{{ $property->bedrooms }}</dd>
                        <dt class="text-[11px] font-bold uppercase leading-tight tracking-wide text-slate-500">Bedrooms</dt>
                    </div>
                </div>
                <div class="rounded-xl bg-slate-50 px-2.5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="size-5 shrink-0 text-orange-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5M4 12h16M4 12v7M20 12v7M7 12v-2a1 1 0 0 1 1-1h3v3M13 12V9h3a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <dd class="text-2xl font-extrabold leading-none text-slate-950">{{ $property->beds }}</dd>
                        <dt class="text-[11px] font-bold uppercase leading-tight tracking-wide text-slate-500">Beds</dt>
                    </div>
                </div>
                <div class="rounded-xl bg-slate-50 px-2.5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="size-5 shrink-0 text-orange-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 11a4 4 0 1 0-8 0M4.5 20a7.5 7.5 0 0 1 15 0M17 7a3 3 0 0 1 0 6M21 20a5 5 0 0 0-4-4.9M7 7a3 3 0 0 0 0 6M3 20a5 5 0 0 1 4-4.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <dd class="text-2xl font-extrabold leading-none text-slate-950">{{ $property->capacity }}</dd>
                        <dt class="text-[11px] font-bold uppercase leading-tight tracking-wide text-slate-500">Guests</dt>
                    </div>
                </div>
            </dl>
            <div class="mt-5 flex items-end justify-between gap-3"><p class="text-3xl font-black">₦{{ number_format((float) $property->default_nightly_price) }} <span class="text-sm font-normal text-gray-500">/ night</span></p>@if($property->reviews->isNotEmpty())<a href="{{ route('marketplace.reviews',$property->marketplaceListing->slug) }}" class="text-sm font-black text-orange-600">★ {{ number_format((float)$property->reviews->avg('rating'),1) }} ({{ $property->reviews->count() }})</a>@endif</div>
            <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" aria-label="Host information">
                <div class="flex items-center gap-4">
                    <div class="relative flex size-14 shrink-0 items-center justify-center rounded-full bg-orange-50 text-lg font-black text-orange-700 ring-1 ring-orange-100">{{ $hostInitials }}</div>
                    <div class="min-w-0">
                        <p class="text-base font-extrabold text-slate-950">Hosted by {{ $hostDisplayName }}</p>
                        <p class="mt-1 text-sm text-slate-500">Verified Shortlet host{{ $hostingYears ? ' · '.$hostingYears.' '.Str::plural('year', $hostingYears).' hosting' : '' }}</p>
                    </div>
                </div>
            </section>
            <div class="mt-5 rounded-xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase text-slate-400">Cancellation</p><p class="mt-2 text-sm font-semibold">Full refund more than 48 hours before check-in.</p></div>
            @if($property->houseRules->isNotEmpty())<div class="mt-4 rounded-xl border border-slate-200 p-4"><h2 class="text-sm font-extrabold">House rules</h2><ul class="mt-2 grid gap-1 text-sm text-slate-600 sm:grid-cols-2">@foreach($property->houseRules as $rule)<li>• {{ $rule->name }}</li>@endforeach</ul></div>@endif
            @auth
                <form x-ref="bookingForm" @submit.prevent="reviewBooking" method="GET" action="{{ route('marketplace.checkout.show', $property->marketplaceListing->slug) }}" class="mt-5 border-t border-slate-200 pt-5">
                    <input type="hidden" name="arrival_date" :value="checkIn"><input type="hidden" name="departure_date" :value="checkOut">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4"><p class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Check in</p><p class="mt-1 text-sm font-extrabold text-slate-950" x-text="checkIn ? formatDate(checkIn) : 'Select above'"></p></div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4"><p class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">Check out</p><p class="mt-1 text-sm font-extrabold text-slate-950" x-text="checkOut ? formatDate(checkOut) : 'Select above'"></p></div>
                    </div>
                    <section class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-4 py-3.5">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-950">Guest details</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500">Who will be staying?</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-orange-700" x-text="guestCount+' of '+capacity"></span>
                        </div>
                        <div class="divide-y divide-slate-100 px-4">
                            <div class="py-3.5">
                                <div class="flex items-center justify-between gap-3">
                                    <div><p class="text-sm font-extrabold text-slate-900">Adults</p><p class="text-xs text-slate-400">Age 13+</p></div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <button type="button" aria-label="Remove adult" @click="adults=Math.max(1,adults-1);children=Math.min(children,capacity-adults)" class="flex size-8 items-center justify-center rounded-full border border-slate-300 bg-white text-base font-bold text-slate-700 disabled:cursor-not-allowed disabled:opacity-35" :disabled="adults<=1">−</button>
                                        <input required aria-label="Adult count" type="number" name="adult_count" x-model.number="adults" @input="adults=Math.max(1,Math.min(capacity,Number(adults||1)));children=Math.min(children,capacity-adults)" min="1" :max="capacity" class="!min-h-0 w-8 border-0 bg-transparent !p-0 text-center text-base font-extrabold text-slate-950 focus:ring-0">
                                        <button type="button" aria-label="Add adult" @click="adults=Math.min(capacity,adults+1);children=Math.min(children,capacity-adults)" class="flex size-8 items-center justify-center rounded-full bg-slate-950 text-base font-bold text-white disabled:cursor-not-allowed disabled:opacity-35" :disabled="guestCount>=capacity">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="py-3.5">
                                <div class="flex items-center justify-between gap-3">
                                    <div><p class="text-sm font-extrabold text-slate-900">Children</p><p class="text-xs text-slate-400">Age 0–12</p></div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <button type="button" aria-label="Remove child" @click="children=Math.max(0,children-1)" class="flex size-8 items-center justify-center rounded-full border border-slate-300 bg-white text-base font-bold text-slate-700 disabled:cursor-not-allowed disabled:opacity-35" :disabled="children<=0">−</button>
                                        <input aria-label="Child count" type="number" name="child_count" x-model.number="children" @input="children=Math.max(0,Math.min(Math.max(0,capacity-adults),Number(children||0)))" min="0" :max="Math.max(0,capacity-adults)" class="!min-h-0 w-8 border-0 bg-transparent !p-0 text-center text-base font-extrabold text-slate-950 focus:ring-0">
                                        <button type="button" aria-label="Add child" @click="children=Math.min(Math.max(0,capacity-adults),children+1)" class="flex size-8 items-center justify-center rounded-full bg-slate-950 text-base font-bold text-white disabled:cursor-not-allowed disabled:opacity-35" :disabled="guestCount>=capacity">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Estimated total</p>
                                <p class="mt-1 text-[11px] text-slate-500" x-text="checkIn && checkOut ? nights+' night'+(nights===1?'':'s')+' · '+guestCount+' guest'+(guestCount===1?'':'s') : 'Select check-in and checkout to see the total'"></p>
                                <p x-show="discount > 0" class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-emerald-700" x-text="'Best value · save '+formatMoney(discount)"></p>
                            </div>
                            <p class="shrink-0 text-right text-xl font-extrabold text-orange-600" x-text="checkIn && checkOut ? formatMoney(total) : '—'"></p>
                        </div>
                        <div x-show="checkIn && checkOut" class="mt-3 space-y-1 border-t border-slate-100 pt-3 text-xs text-slate-600">
                            <div class="flex justify-between gap-3"><span x-text="formatMoney(nightlyRate)+' × '+nights+' night'+(nights===1?'':'s')"></span><strong class="text-slate-900" x-text="formatMoney(subtotal)"></strong></div>
                            <div x-show="discount > 0" class="flex justify-between gap-3 text-emerald-700"><span x-text="'Longer-stay discount ('+discountPercentage+'%)'"></span><strong x-text="'−'+formatMoney(discount)"></strong></div>
                            <p x-show="discount === 0 && nextDiscountHint" class="rounded-lg bg-slate-50 p-2 text-[11px] font-semibold text-slate-500" x-text="nextDiscountHint"></p>
                            <p class="pt-1 text-[11px] text-slate-400">Tweak your dates to compare totals. Final availability and price are rechecked before confirmation. No separate service fee is added.</p>
                        </div>
                    </div>
                    <p class="mt-3 flex items-start gap-2 text-[11px] leading-5 text-slate-500"><svg class="mt-0.5 size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 10V8a5 5 0 0 1 10 0v2m-11 0h12v10H6V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Final availability and amount are securely rechecked before confirmation.</span></p>
                    <p x-show="guestCount > capacity" class="mt-3 rounded-lg bg-red-50 p-3 text-xs font-semibold text-red-700">This property accommodates a maximum of <span x-text="capacity"></span> guests.</p>
                    <p x-show="quoteError" x-text="quoteError" class="mt-3 rounded-lg bg-red-50 p-3 text-xs font-semibold text-red-700"></p>
                    <button :disabled="quoteLoading||!checkIn||!checkOut||guestCount<1||guestCount>capacity" class="mt-4 w-full rounded-2xl bg-orange-500 py-3.5 font-extrabold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-40 disabled:shadow-none" x-text="quoteLoading?'Checking price & availability…':'Continue to checkout'"></button>
                </form>
            @else
                <div class="mt-8 rounded-2xl bg-gray-50 p-5 text-center">
                    <p class="text-sm text-gray-600">Create an account to book securely and manage this stay whenever you return.</p>
                    <a href="{{ route('register', ['redirect' => request()->getRequestUri()]) }}" class="mt-4 block rounded-xl bg-orange-500 py-3 font-bold text-white hover:bg-orange-600">Create account to book</a>
                    <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="mt-3 inline-block text-sm font-semibold text-gray-700 hover:text-orange-600">Already have an account? Log in</a>
                </div>
            @endauth
        </section>
    </div>

    <section class="mt-8 border-t border-slate-200 pt-8" aria-labelledby="guest-reviews-title">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.16em] text-orange-600">Verified stays only</p>
                <h2 id="guest-reviews-title" class="mt-2 text-2xl font-extrabold text-slate-950">Guest reviews</h2>
            </div>
            @if($property->reviews->isNotEmpty())
                <a href="{{ route('marketplace.reviews', $property->marketplaceListing->slug) }}" class="rounded-2xl bg-orange-50 px-5 py-3 text-right transition hover:bg-orange-100">
                    <p class="text-2xl font-extrabold text-orange-700">★ {{ number_format((float) $property->reviews->avg('rating'), 1) }}</p>
                    <p class="text-xs font-semibold text-orange-800">{{ $property->reviews->count() }} {{ Str::plural('review', $property->reviews->count()) }}</p>
                </a>
            @endif
        </div>

        @if($property->reviews->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="font-extrabold text-slate-800">No guest reviews yet</p>
                <p class="mt-2 text-sm text-slate-500">Only guests who complete a booking can review this property.</p>
            </div>
        @else
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach($property->reviews->take(4) as $review)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-extrabold text-slate-900">{{ $review->guest->name }}</p>
                                <p class="mt-1 text-xs font-bold text-emerald-700">✓ Verified stay</p>
                            </div>
                            <div class="text-right"><p class="font-extrabold text-orange-600">{{ str_repeat('★', $review->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - $review->rating) }}</span></p><p class="mt-1 text-xs text-slate-400">{{ $review->published_at->format('M Y') }}</p></div>
                        </div>
                        @if($review->title)<h3 class="mt-4 font-extrabold text-slate-900">{{ $review->title }}</h3>@endif
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $review->content }}</p>
                        @if($review->response && $review->response->moderation_status === 'approved' && $review->response->published_at)
                            <div class="mt-4 rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Response from the property owner</p>
                                <p class="mt-2 text-sm leading-6 text-slate-700">{{ $review->response->content }}</p>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
            <a href="{{ route('marketplace.reviews', $property->marketplaceListing->slug) }}" class="mt-5 inline-flex rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white">Read all reviews</a>
        @endif
    </section>
</div>
</main>
@endsection

@push('scripts')
<script>
function stayBookingCalendar(intervals, gallery, nightlyRate, capacity, promotions, quoteUrl) {
    return {gallery,nightlyRate:Number(nightlyRate),capacity:Number(capacity),promotions,quoteUrl,serverQuote:null,quoteLoading:false,quoteError:'',activeIndex:0,offset:0,months:[],checkIn:'',checkOut:'',adults:1,children:0,selectionError:'',jumpMonth:'',minimumMonth:'',maximumMonth:'',
        get activeMedia(){return this.gallery[this.activeIndex]},
        get guestCount(){return Number(this.adults||0)+Number(this.children||0)},
        get nights(){if(this.serverQuote)return Number(this.serverQuote.nights);if(!this.checkIn||!this.checkOut)return 0;return Math.round((new Date(this.checkOut+'T00:00:00')-new Date(this.checkIn+'T00:00:00'))/86400000)},
        get subtotal(){return this.serverQuote?Number(this.serverQuote.subtotal):this.nightlyRate*this.nights},
        get discountPercentage(){return this.serverQuote&&this.subtotal>0?Math.round((Number(this.serverQuote.discount)/this.subtotal)*100):this.promotions.filter(p=>this.nights>=Number(p.minimum_nights||1)).reduce((best,p)=>Math.max(best,Number(p.percentage||0)),0)},
        get discount(){return this.serverQuote?Number(this.serverQuote.discount):Math.round(this.subtotal*(this.discountPercentage/100))},
        get total(){return this.serverQuote?Number(this.serverQuote.total):this.subtotal-this.discount},
        get nextDiscountHint(){if(!this.checkIn||!this.checkOut||this.discount>0)return '';const next=this.promotions.map(p=>({nights:Number(p.minimum_nights||1),percentage:Number(p.percentage||0)})).filter(p=>p.nights>this.nights).sort((a,b)=>a.nights-b.nights)[0];return next?'Stay '+(next.nights-this.nights)+' more night'+(next.nights-this.nights===1?'':'s')+' to unlock '+next.percentage+'% longer-stay pricing.':''},
        formatMoney(value){return new Intl.NumberFormat('en-NG',{style:'currency',currency:'NGN',maximumFractionDigits:0}).format(value)},
        async reviewBooking(){this.quoteLoading=true;this.quoteError='';this.serverQuote=null;try{const response=await fetch(this.quoteUrl,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({arrival_date:this.checkIn,departure_date:this.checkOut,adult_count:this.adults,child_count:this.children})});const data=await response.json();if(!response.ok){const errors=data.errors||{};throw new Error(Object.values(errors).flat()[0]||data.message||'We could not verify this stay.')}this.serverQuote=data;this.$nextTick(()=>this.$refs.bookingForm.submit())}catch(error){this.quoteError=error.message}finally{this.quoteLoading=false}},
        init(){const now=new Date();this.minimumMonth=this.monthValue(now);this.maximumMonth=this.monthValue(new Date(now.getFullYear(),now.getMonth()+17,1));this.jumpMonth=this.minimumMonth;this.buildMonths()},
        monthValue(d){return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')},
        iso(d){return [d.getFullYear(),String(d.getMonth()+1).padStart(2,'0'),String(d.getDate()).padStart(2,'0')].join('-')},
        isBlocked(date){return intervals.some(range=>date>=range.start&&date<range.end)},
        buildMonths(){const today=new Date();today.setHours(0,0,0,0);this.months=[0].map(add=>{const first=new Date(today.getFullYear(),today.getMonth()+this.offset+add,1),count=new Date(first.getFullYear(),first.getMonth()+1,0).getDate();return {key:this.monthValue(first),label:first.toLocaleDateString('en-GB',{month:'long',year:'numeric'}),offset:first.getDay(),days:Array.from({length:count},(_,i)=>{const date=new Date(first.getFullYear(),first.getMonth(),i+1),iso=this.iso(date);return {number:i+1,date:iso,past:date<today,blocked:this.isBlocked(iso)}})}});this.jumpMonth=this.months[0].key},
        previous(){this.offset=Math.max(0,this.offset-1);this.buildMonths()},next(){this.offset=Math.min(17,this.offset+1);this.buildMonths()},
        jumpToMonth(){const [y,m]=this.jumpMonth.split('-').map(Number),now=new Date();this.offset=Math.max(0,Math.min(17,(y-now.getFullYear())*12+(m-1-now.getMonth())));this.buildMonths()},
        selectDate(day){this.serverQuote=null;this.selectionError='';if(day.past||day.blocked)return;if(this.checkIn&&day.date===this.checkIn&&!this.checkOut){this.checkIn='';return}if(!this.checkIn||this.checkOut||day.date<=this.checkIn){this.checkIn=day.date;this.checkOut='';return}let cursor=new Date(this.checkIn+'T00:00:00');const end=new Date(day.date+'T00:00:00');while(cursor<end){if(this.isBlocked(this.iso(cursor))){this.selectionError='That range includes a booked date. Choose a different checkout date.';return}cursor.setDate(cursor.getDate()+1)}this.checkOut=day.date},
        dayClass(day){if(day.past)return 'text-slate-300';if(day.blocked)return 'bg-red-100 font-bold text-red-600 line-through';if(day.date===this.checkIn||day.date===this.checkOut)return 'bg-orange-500 font-bold text-white';if(this.checkIn&&this.checkOut&&day.date>this.checkIn&&day.date<this.checkOut)return 'bg-orange-100 font-bold text-orange-800';return 'bg-emerald-200 font-bold text-emerald-900 ring-1 ring-emerald-300 hover:bg-emerald-300'},
        formatDate(value){return new Date(value+'T00:00:00').toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'})},
        get selectionText(){return this.checkIn?(this.checkOut?this.formatDate(this.checkIn)+' → '+this.formatDate(this.checkOut):'Now choose checkout'):'Select check-in, then checkout'}
    }
}
</script>
@endpush
