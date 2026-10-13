@props(['name'])
<svg {{ $attributes->merge(['class' => 'size-5 shrink-0', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('dashboard')<path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h3A1.5 1.5 0 0 1 10 5.5v3A1.5 1.5 0 0 1 8.5 10h-3A1.5 1.5 0 0 1 4 8.5v-3Zm10 0A1.5 1.5 0 0 1 15.5 4h3A1.5 1.5 0 0 1 20 5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 14 8.5v-3Zm-10 10A1.5 1.5 0 0 1 5.5 14h3a1.5 1.5 0 0 1 1.5 1.5v3A1.5 1.5 0 0 1 8.5 20h-3A1.5 1.5 0 0 1 4 18.5v-3Zm10 0a1.5 1.5 0 0 1 1.5-1.5h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a1.5 1.5 0 0 1-1.5-1.5v-3Z"/>@break
        @case('bell')<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('building')<path d="M4 21V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v16M8 7h2m3 0h1M8 11h2m3 0h1M8 15h2m3 0h1M2 21h20m-9 0v-3H8v3m9-12h2a1 1 0 0 1 1 1v11" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('booking')<path d="M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm3 8h3v3H8v-3Z" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('message')<path d="M20 15a4 4 0 0 1-4 4H8l-5 2 1.6-4A7 7 0 0 1 3 12V8a4 4 0 0 1 4-4h9a4 4 0 0 1 4 4v7ZM8 9h8m-8 4h5" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('calendar')<path d="M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('finance')<path d="M4 20V10m5 10V4m6 16v-7m5 7V7M2 20h20" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('operations')<path d="m14.7 6.3 3-3 3 3-3 3m-8.4 8.4-3 3-3-3 3-3M18 9.3V15a3 3 0 0 1-3 3H9M6 14.7V9a3 3 0 0 1 3-3h5" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('team')<path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-1a3 3 0 0 1 0 6m3 4v-1.5a3.5 3.5 0 0 0-2-3.15" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('menu')<path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>@break
        @case('close')<path d="m6 6 12 12M18 6 6 18" stroke-linecap="round"/>@break
        @case('mail')<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6" stroke-linejoin="round"/>@break
        @case('lock')<rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>@break
        @case('search')<circle cx="11" cy="11" r="7"/><path d="m16 16 4 4" stroke-linecap="round"/>@break
        @case('arrow-right')<path d="m9 5 7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('home')<path d="M4 11.5 12 5l8 6.5V20H4v-8.5ZM9 20v-6h6v6" stroke-linecap="round" stroke-linejoin="round"/>@break
        @case('location')<path d="M12 21s7-6.25 7-12a7 7 0 1 0-14 0c0 5.75 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/>@break
    @endswitch
</svg>
