@extends('layouts.app')

@section('content')
<x-navbar />
<main class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    @php
        $coverMedia = $property->media->firstWhere('is_primary', true) ?? $property->media->firstWhere('media_type', \App\Enums\PropertyMediaType::Image);
        $coverImage = $coverMedia?->external_url ?: ($coverMedia?->storage_path ? '/storage/'.ltrim($coverMedia->storage_path, '/') : '/image.png');
        $blockedIntervals = $property->bookings->map(fn($booking) => ['start' => $booking->arrival_date->toDateString(), 'end' => $booking->departure_date->toDateString()])
            ->concat($property->availabilityBlocks->map(fn($block) => ['start' => $block->starts_on->toDateString(), 'end' => $block->ends_on->toDateString()]))->values();
        $gallery = $property->media->map(fn($media) => ['url' => $media->external_url ?: ($media->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png'), 'type' => $media->media_type->value, 'title' => $media->title ?: $property->name])->values();
        if ($gallery->isEmpty()) $gallery->push(['url' => '/image.png', 'type' => 'image', 'title' => $property->name]);
        $promotions = $property->promotions->filter(fn($promotion) => $promotion->discount_type->value === 'percentage')->map(fn($promotion) => ['minimum_nights' => $promotion->minimum_stay_nights ?? 1, 'percentage' => (float)$promotion->discount_value])->values();
    @endphp
    <a href="{{ route('home') }}" class="text-sm text-orange-600">← Back to all stays</a>
    <div class="grid lg:grid-cols-2 gap-10 mt-6" x-data="stayBookingCalendar(@js($blockedIntervals), @js($gallery), {{ (float)$property->default_nightly_price }}, {{ $property->capacity }}, @js($promotions), @js(route('marketplace.checkout.quote', $property->marketplaceListing->slug)))">
        <div class="min-w-0">
            <div class="relative aspect-[4/3] overflow-hidden rounded-3xl bg-slate-900 shadow-sm">
                <template x-if="activeMedia.type==='video'"><video controls preload="metadata" :src="activeMedia.url" class="h-full w-full object-contain"></video></template>
                <template x-if="activeMedia.type==='image'"><img :src="activeMedia.url" x-on:error="$event.target.src='/image.png'" :alt="activeMedia.title" class="h-full w-full object-cover"></template>
                <span x-show="activeMedia.type==='video'" class="absolute left-4 top-4 rounded-full bg-black/70 px-3 py-1 text-xs font-bold text-white">VIDEO</span>
            </div>
            <div class="mt-3 flex gap-3 overflow-x-auto pb-2"><template x-for="(media,index) in gallery" :key="media.url+index"><button type="button" @click="activeIndex=index" class="relative h-20 w-24 shrink-0 overflow-hidden rounded-xl border-2 bg-slate-100 transition" :class="activeIndex===index?'border-orange-500 ring-2 ring-orange-100':'border-transparent opacity-75 hover:opacity-100'"><template x-if="media.type==='image'"><img :src="media.url" :alt="media.title" class="h-full w-full object-cover"></template><template x-if="media.type==='video'"><div class="flex h-full w-full items-center justify-center bg-slate-800 text-white"><svg class="size-7" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div></template><span x-show="media.type==='video'" class="absolute bottom-1 right-1 rounded bg-black/70 px-1.5 py-0.5 text-[9px] font-bold text-white">VIDEO</span></button></template></div>
        </div>
        <section>
            @if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>@endif
            <span class="text-xs font-bold text-emerald-700">✓ Verified serviced apartment</span>
            <h1 class="text-4xl font-extrabold mt-3">{{ $property->marketplaceListing->public_title }}</h1>
            <p class="text-gray-500 mt-2">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }}</p>
            <p class="mt-6 text-gray-700">{{ $property->marketplaceListing->public_description }}</p>
            <div class="flex gap-5 mt-6 text-sm"><span>{{ $property->bedrooms }} bedrooms</span><span>{{ $property->beds }} beds</span><span>{{ $property->capacity }} guests</span></div>
            <p class="text-2xl font-bold mt-8">₦{{ number_format((float) $property->default_nightly_price) }} <span class="text-sm font-normal text-gray-500">/ night</span></p>
            <div class="mt-6 grid gap-3 sm:grid-cols-2"><div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase text-slate-400">Arrival & departure</p><p class="mt-2 text-sm font-semibold">Check-in {{ substr($property->marketplaceListing->check_in_time ?: '14:00',0,5) }} · Check-out {{ substr($property->marketplaceListing->check_out_time ?: '11:00',0,5) }}</p></div><div class="rounded-xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase text-slate-400">Cancellation</p><p class="mt-2 text-sm font-semibold">Full refund more than 48 hours before check-in.</p></div></div>
            @if($property->houseRules->isNotEmpty())<div class="mt-4 rounded-xl border border-slate-200 p-4"><h2 class="text-sm font-extrabold">House rules</h2><ul class="mt-2 grid gap-1 text-sm text-slate-600 sm:grid-cols-2">@foreach($property->houseRules as $rule)<li>• {{ $rule->name }}</li>@endforeach</ul></div>@endif
            <div class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="font-extrabold text-slate-900">Choose your stay dates</h2><p class="mt-1 text-xs text-slate-500">Booked dates include Verified Shortlet and synchronized iCal reservations.</p></div><label class="text-xs font-bold text-slate-600">Jump to month <input type="month" x-model="jumpMonth" :min="minimumMonth" :max="maximumMonth" @change="jumpToMonth" class="ml-1 rounded-lg border-slate-200 py-1.5 text-xs"></label></div>
                <div class="p-4"><div class="mb-4 flex items-center justify-between rounded-xl bg-slate-50 p-2"><button type="button" @click="previous" :disabled="offset===0" class="rounded-lg px-3 py-2 text-sm font-bold disabled:opacity-30">←</button><div class="text-center"><p class="text-xs font-bold text-slate-700" x-text="selectionText"></p><p class="text-[11px] text-slate-400">Browse up to 18 months ahead</p></div><button type="button" @click="next" :disabled="offset>=16" class="rounded-lg px-3 py-2 text-sm font-bold disabled:opacity-30">→</button></div>
                    <div class="grid gap-5 md:grid-cols-2"><template x-for="month in months" :key="month.key"><section><h3 class="mb-3 text-center text-sm font-extrabold" x-text="month.label"></h3><div class="grid grid-cols-7 gap-1 text-center"><template x-for="label in ['S','M','T','W','T','F','S']"><span class="py-1 text-[10px] font-bold text-slate-400" x-text="label"></span></template><template x-for="blank in month.offset"><span></span></template><template x-for="day in month.days" :key="day.date"><button type="button" @click="selectDate(day)" :disabled="day.past||day.blocked" class="flex aspect-square items-center justify-center rounded-lg text-xs transition" :class="dayClass(day)" :title="day.blocked?'Booked or unavailable':'Available'" x-text="day.number"></button></template></div></section></template></div>
                    <div class="mt-4 flex flex-wrap gap-4 text-xs font-semibold"><span class="flex items-center gap-2"><i class="size-3 rounded bg-emerald-50 ring-1 ring-emerald-200"></i>Available</span><span class="flex items-center gap-2"><i class="size-3 rounded bg-red-100 ring-1 ring-red-200"></i>Booked</span><span class="flex items-center gap-2"><i class="size-3 rounded bg-orange-500"></i>Your dates</span></div><p x-show="selectionError" x-text="selectionError" class="mt-3 rounded-lg bg-red-50 p-3 text-xs font-semibold text-red-700"></p>
                </div>
            </div>
            @auth
                <form x-ref="bookingForm" @submit.prevent="reviewBooking" method="POST" action="{{ route('marketplace.checkout.store', $property->marketplaceListing->slug) }}" class="mt-8 grid grid-cols-2 gap-3 rounded-2xl bg-gray-50 p-5">
                    @csrf
                    <input type="hidden" name="arrival_date" :value="checkIn"><input type="hidden" name="departure_date" :value="checkOut">
                    <div class="rounded-xl border border-slate-200 bg-white p-3"><p class="text-[10px] font-bold uppercase text-slate-400">Check in</p><p class="mt-1 text-sm font-bold" x-text="checkIn ? formatDate(checkIn) : 'Select above'"></p></div>
                    <div class="rounded-xl border border-slate-200 bg-white p-3"><p class="text-[10px] font-bold uppercase text-slate-400">Check out</p><p class="mt-1 text-sm font-bold" x-text="checkOut ? formatDate(checkOut) : 'Select above'"></p></div>
                    <label class="text-xs font-bold">Adults<input required type="number" name="adult_count" x-model.number="adults" @input="children=Math.min(children,capacity-adults)" min="1" :max="capacity" class="mt-1 w-full rounded-xl border-gray-300"></label>
                    <label class="text-xs font-bold">Children<input type="number" name="child_count" x-model.number="children" min="0" :max="Math.max(0,capacity-adults)" class="mt-1 w-full rounded-xl border-gray-300"></label>
                    <label class="col-span-2 text-xs font-bold">Guest phone number<input name="guest_phone" required maxlength="32" value="{{ old('guest_phone',auth()->user()?->phone_number) }}" class="mt-1 w-full rounded-xl border-gray-300" placeholder="e.g. +234 801 234 5678"></label><label class="col-span-2 text-xs font-bold">Special requests <span class="font-normal text-slate-400">(optional)</span><textarea name="special_requests" maxlength="1000" rows="3" class="mt-1 w-full rounded-xl border-gray-300" placeholder="Arrival time, accessibility needs or anything the host should prepare"></textarea></label>
                    <input type="hidden" name="quoted_total" :value="serverQuote?.total ?? total">
                    <input type="hidden" name="idempotency_key" value="{{ (string) Str::uuid() }}">
                    <p class="col-span-2 text-xs text-gray-500">Payment is simulated in this development version. Your final amount is calculated securely before confirmation.</p>
                    <p x-show="guestCount > capacity" class="col-span-2 rounded-lg bg-red-50 p-3 text-xs font-semibold text-red-700">This property accommodates a maximum of <span x-text="capacity"></span> guests.</p>
                    <p x-show="quoteError" x-text="quoteError" class="col-span-2 rounded-lg bg-red-50 p-3 text-xs font-semibold text-red-700"></p>
                    <button :disabled="quoteLoading||!checkIn||!checkOut||guestCount<1||guestCount>capacity" class="col-span-2 rounded-xl bg-orange-500 py-3 font-bold text-white disabled:cursor-not-allowed disabled:opacity-40" x-text="quoteLoading?'Checking price & availability…':'Review booking'"></button>
                </form>
                <div x-show="confirmOpen" x-cloak @keydown.escape.window="if(!submitting)confirmOpen=false" class="fixed inset-0 z-[80] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="booking-confirm-title">
                    <button type="button" @click="confirmOpen=false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" aria-label="Close confirmation"></button>
                    <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white shadow-2xl">
                        <div class="bg-gradient-to-br from-orange-50 via-white to-amber-50 p-6 text-center">
                            <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-orange-100 text-orange-600"><svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg></div>
                            <h2 id="booking-confirm-title" class="mt-4 text-2xl font-extrabold text-slate-900">Confirm your stay</h2>
                            <p class="mt-2 text-sm text-slate-500">Review your dates, guests and complete price breakdown.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 border-y border-slate-100 p-5 text-sm">
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Check in</p><p class="mt-1 font-bold" x-text="formatDate(checkIn)"></p></div>
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Check out</p><p class="mt-1 font-bold" x-text="formatDate(checkOut)"></p></div>
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Length of stay</p><p class="mt-1 font-bold" x-text="nights+' night'+(nights===1?'':'s')"></p></div>
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Guests</p><p class="mt-1 font-bold" x-text="guestCount+' guest'+(guestCount===1?'':'s')"></p><p class="mt-0.5 text-[10px] text-slate-400" x-text="adults+' adult'+(adults===1?'':'s')+(children ? ' · '+children+' child'+(children===1?'':'ren') : '')"></p></div>
                        </div>
                        <div class="space-y-3 p-5 text-sm">
                            <div class="flex justify-between gap-4 text-slate-600"><span x-text="formatMoney(nightlyRate)+' × '+nights+' night'+(nights===1?'':'s')"></span><strong class="text-slate-900" x-text="formatMoney(subtotal)"></strong></div>
                            <div x-show="discount > 0" class="flex justify-between gap-4 text-emerald-700"><span>Eligible stay discount</span><strong x-text="'−'+formatMoney(discount)"></strong></div>
                            <div class="flex justify-between gap-4 text-slate-600"><span>Service fee (5%)</span><strong class="text-slate-900" x-text="formatMoney(serviceFee)"></strong></div>
                            <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-4"><span class="font-extrabold text-slate-900">Total amount</span><strong class="text-xl text-orange-600" x-text="formatMoney(total)"></strong></div>
                            <p class="rounded-xl bg-blue-50 p-3 text-xs leading-5 text-blue-800">We recheck availability, promotions and pricing securely when you confirm. Payment is simulated in this development version.</p>
                        </div>
                        <div class="flex flex-col-reverse gap-3 px-5 pb-5 sm:flex-row sm:justify-end"><button type="button" @click="confirmOpen=false" :disabled="submitting" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">Change details</button><button type="button" @click="submitting=true;$refs.bookingForm.submit()" :disabled="submitting||guestCount<1||guestCount>capacity" class="rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white disabled:opacity-60" x-text="submitting?'Booking…':'Confirm & book · '+formatMoney(total)"></button></div>
                    </div>
                </div>
            @else
                <div class="mt-8 rounded-2xl bg-gray-50 p-5 text-center">
                    <p class="text-sm text-gray-600">Create an account to book securely and manage this stay whenever you return.</p>
                    <a href="{{ route('register', ['redirect' => request()->getRequestUri()]) }}" class="mt-4 block rounded-xl bg-orange-500 py-3 font-bold text-white hover:bg-orange-600">Create account to book</a>
                    <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="mt-3 inline-block text-sm font-semibold text-gray-700 hover:text-orange-600">Already have an account? Log in</a>
                </div>
            @endauth
        </section>
    </div>

    <section class="mt-12 border-t border-slate-200 pt-10" aria-labelledby="guest-reviews-title">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.16em] text-orange-600">Verified stays only</p>
                <h2 id="guest-reviews-title" class="mt-2 text-2xl font-extrabold text-slate-950">Guest reviews</h2>
            </div>
            @if($property->reviews->isNotEmpty())
                <div class="rounded-2xl bg-orange-50 px-5 py-3 text-right">
                    <p class="text-2xl font-extrabold text-orange-700">★ {{ number_format((float) $property->reviews->avg('rating'), 1) }}</p>
                    <p class="text-xs font-semibold text-orange-800">{{ $property->reviews->count() }} {{ Str::plural('review', $property->reviews->count()) }}</p>
                </div>
            @endif
        </div>

        @if($property->reviews->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="font-extrabold text-slate-800">No guest reviews yet</p>
                <p class="mt-2 text-sm text-slate-500">Only guests who complete a booking can review this property.</p>
            </div>
        @else
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach($property->reviews as $review)
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
        @endif
    </section>
</main>
@endsection

@push('scripts')
<script>
function stayBookingCalendar(intervals, gallery, nightlyRate, capacity, promotions, quoteUrl) {
    return {gallery,nightlyRate:Number(nightlyRate),capacity:Number(capacity),promotions,quoteUrl,serverQuote:null,quoteLoading:false,quoteError:'',activeIndex:0,offset:0,months:[],checkIn:'',checkOut:'',adults:1,children:0,confirmOpen:false,submitting:false,selectionError:'',jumpMonth:'',minimumMonth:'',maximumMonth:'',
        get activeMedia(){return this.gallery[this.activeIndex]},
        get guestCount(){return Number(this.adults||0)+Number(this.children||0)},
        get nights(){if(this.serverQuote)return Number(this.serverQuote.nights);if(!this.checkIn||!this.checkOut)return 0;return Math.round((new Date(this.checkOut+'T00:00:00')-new Date(this.checkIn+'T00:00:00'))/86400000)},
        get subtotal(){return this.serverQuote?Number(this.serverQuote.subtotal):this.nightlyRate*this.nights},
        get discountPercentage(){return this.promotions.filter(p=>this.nights>=Number(p.minimum_nights||1)).reduce((best,p)=>Math.max(best,Number(p.percentage||0)),0)},
        get discount(){return this.serverQuote?Number(this.serverQuote.discount):Math.round(this.subtotal*(this.discountPercentage/100))},
        get serviceFee(){return this.serverQuote?Number(this.serverQuote.service_fee):Math.round((this.subtotal-this.discount)*.05)},
        get total(){return this.serverQuote?Number(this.serverQuote.total):this.subtotal-this.discount+this.serviceFee},
        formatMoney(value){return new Intl.NumberFormat('en-NG',{style:'currency',currency:'NGN',maximumFractionDigits:0}).format(value)},
        async reviewBooking(){this.quoteLoading=true;this.quoteError='';this.serverQuote=null;try{const response=await fetch(this.quoteUrl,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({arrival_date:this.checkIn,departure_date:this.checkOut,adult_count:this.adults,child_count:this.children})});const data=await response.json();if(!response.ok){const errors=data.errors||{};throw new Error(Object.values(errors).flat()[0]||data.message||'We could not verify this stay.')}this.serverQuote=data;this.confirmOpen=true}catch(error){this.quoteError=error.message}finally{this.quoteLoading=false}},
        init(){const now=new Date();this.minimumMonth=this.monthValue(now);this.maximumMonth=this.monthValue(new Date(now.getFullYear(),now.getMonth()+17,1));this.jumpMonth=this.minimumMonth;this.buildMonths()},
        monthValue(d){return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')},
        iso(d){return [d.getFullYear(),String(d.getMonth()+1).padStart(2,'0'),String(d.getDate()).padStart(2,'0')].join('-')},
        isBlocked(date){return intervals.some(range=>date>=range.start&&date<range.end)},
        buildMonths(){const today=new Date();today.setHours(0,0,0,0);this.months=[0,1].map(add=>{const first=new Date(today.getFullYear(),today.getMonth()+this.offset+add,1),count=new Date(first.getFullYear(),first.getMonth()+1,0).getDate();return {key:this.monthValue(first),label:first.toLocaleDateString('en-GB',{month:'long',year:'numeric'}),offset:first.getDay(),days:Array.from({length:count},(_,i)=>{const date=new Date(first.getFullYear(),first.getMonth(),i+1),iso=this.iso(date);return {number:i+1,date:iso,past:date<today,blocked:this.isBlocked(iso)}})}});this.jumpMonth=this.months[0].key},
        previous(){this.offset=Math.max(0,this.offset-1);this.buildMonths()},next(){this.offset=Math.min(16,this.offset+1);this.buildMonths()},
        jumpToMonth(){const [y,m]=this.jumpMonth.split('-').map(Number),now=new Date();this.offset=Math.max(0,Math.min(16,(y-now.getFullYear())*12+(m-1-now.getMonth())));this.buildMonths()},
        selectDate(day){this.serverQuote=null;this.selectionError='';if(!this.checkIn||this.checkOut||day.date<=this.checkIn){this.checkIn=day.date;this.checkOut='';return}let cursor=new Date(this.checkIn+'T00:00:00');const end=new Date(day.date+'T00:00:00');while(cursor<end){if(this.isBlocked(this.iso(cursor))){this.selectionError='That range includes a booked date. Choose a different checkout date.';return}cursor.setDate(cursor.getDate()+1)}this.checkOut=day.date},
        dayClass(day){if(day.past)return 'text-slate-300';if(day.blocked)return 'bg-red-100 font-bold text-red-600 line-through';if(day.date===this.checkIn||day.date===this.checkOut)return 'bg-orange-500 font-bold text-white';if(this.checkIn&&this.checkOut&&day.date>this.checkIn&&day.date<this.checkOut)return 'bg-orange-100 font-bold text-orange-800';return 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100'},
        formatDate(value){return new Date(value+'T00:00:00').toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'})},
        get selectionText(){return this.checkIn?(this.checkOut?this.formatDate(this.checkIn)+' → '+this.formatDate(this.checkOut):'Now choose checkout'):'Select check-in, then checkout'}
    }
}
</script>
@endpush
