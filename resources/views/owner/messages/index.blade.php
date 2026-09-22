<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Messages – {{ $business->name }}</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{sidebarOpen:false}">
<div class="flex min-h-screen">@include('partials.sidebar-nav',['active'=>'messages'])<div class="min-w-0 flex-1">
    <header class="border-b border-slate-200 bg-white px-4 py-4 lg:px-8"><p class="text-xs font-black uppercase tracking-[.18em] text-orange-600">Booking communication</p><h1 class="mt-1 text-2xl font-black">Messages</h1><p class="mt-1 text-sm text-slate-500">Guest conversations across bookings you can access.</p></header>
    <main class="mx-auto max-w-5xl px-4 py-6 lg:px-8">
        <section class="space-y-4">
            @forelse($bookings as $booking)
                @php($latest = $booking->interactions->first())
                <a href="{{ route('owner.bookings.messages.show', $booking) }}" class="block rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-orange-600">{{ $booking->reference }}</p>
                            <h2 class="mt-1 text-lg font-black text-slate-950">{{ $booking->guest->name }} · {{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $latest?->content ?: 'Open this thread to continue the conversation.' }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $latest?->occurred_at?->diffForHumans() ?: 'No messages' }}</span>
                    </div>
                </a>
            @empty
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <h2 class="text-xl font-black text-slate-950">No guest messages yet</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">Booking conversations will appear here when guests contact the property team.</p>
                </div>
            @endforelse
        </section>
        <div class="mt-7">{{ $bookings->links() }}</div>
    </main>
</div></div>
</body></html>
