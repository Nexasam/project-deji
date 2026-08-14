<div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
    <h3 class="text-base font-bold text-gray-900 mb-4">Quick actions</h3>

    <div class="grid grid-cols-3 gap-3">
        {{-- Add Property --}}
        <button class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-[#FF5A00] hover:bg-orange-50 transition-all">
            <div class="w-10 h-10 rounded-full border-2 border-[#FF5A00] flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-[#FF5A00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-900">Add property</span>
        </button>

        {{-- New Booking --}}
        <button class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-[#FF5A00] hover:bg-orange-50 transition-all">
            <div class="w-10 h-10 rounded-full bg-[#FFF5ED] flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-[#FF5A00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-900">New booking</span>
        </button>

        {{-- Assign Task --}}
        <button class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-[#FF5A00] hover:bg-orange-50 transition-all">
            <div class="w-10 h-10 rounded-full bg-[#FFF5ED] flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-[#FF5A00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-900">Assign task</span>
        </button>
    </div>
</div>
