<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Booking messages – {{ $business->name }}</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{sidebarOpen:false}">
<div class="flex min-h-screen">@include('partials.sidebar-nav',['active'=>'messages'])<div class="min-w-0 flex-1">
    <header class="border-b border-slate-200 bg-white px-4 py-4 lg:px-8"><a href="{{ route('owner.messages.index') }}" class="text-xs font-bold uppercase tracking-wider text-orange-600">← Messages</a><h1 class="mt-1 text-2xl font-black">{{ $booking->guest->name }}</h1><p class="mt-1 text-sm text-slate-500">{{ $booking->reference }} · {{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</p></header>
    <main class="mx-auto max-w-4xl px-4 py-6 lg:px-8">
        @if(session('status'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="space-y-4 bg-slate-50 p-5 sm:p-6">
                @forelse($booking->interactions->where('interaction_type', 'message') as $message)
                    @php($mine = $message->user_id === auth()->id())
                    <article class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm shadow-sm {{ $mine ? 'rounded-tr-sm bg-orange-600 text-white' : 'rounded-tl-sm bg-white text-slate-700' }}">
                            <p class="font-bold {{ $mine ? 'text-orange-100' : 'text-slate-500' }}">{{ $mine ? 'You' : $booking->guest->name }}</p>
                            <p class="mt-1 leading-6">{{ $message->content }}</p>
                            <p class="mt-2 text-right text-[11px] {{ $mine ? 'text-orange-100' : 'text-slate-400' }}">{{ $message->occurred_at->format('d M, H:i') }}</p>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl bg-white p-6 text-center text-sm text-slate-500">No guest messages yet.</div>
                @endforelse
            </div>
            <form method="POST" action="{{ route('owner.bookings.messages.store', $booking) }}" class="border-t border-slate-100 p-5 sm:p-6">@csrf
                <label class="block text-sm font-bold text-slate-700">Reply to guest<textarea name="content" required minlength="2" maxlength="2000" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Type your reply…">{{ old('content') }}</textarea></label>
                <div class="mt-4 flex justify-end"><button class="rounded-xl bg-orange-600 px-5 py-3 text-sm font-black text-white hover:bg-orange-700">Send reply</button></div>
            </form>
        </section>
    </main>
</div></div>
</body></html>
