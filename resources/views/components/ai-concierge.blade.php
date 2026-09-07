@props([
    'title' => 'Ask before you book, not after',
    'subtitle' => 'AI TRIP CONCIERGE',
    'description' => 'Type a question about any neighbourhood, budget or house rule and get an answer grounded in verified listing data.'
])

<section class="bg-gray-900 py-12 sm:py-14" x-data="aiConcierge()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-[1.2fr_.8fr] gap-6 lg:gap-10 items-start">
            <div>
                <p class="text-[11px] font-bold text-orange-500 uppercase tracking-[0.16em]">{{ $subtitle }}</p>
                <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $title }}</h2>
                <p class="mt-2 text-sm leading-6 text-gray-400 max-w-xl">{{ $description }}</p>

                <div class="mt-6 rounded-2xl border border-white/10 bg-gray-800/80 p-4 sm:p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span></span>
                        <span class="text-sm font-bold text-white">AI Trip Concierge</span>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                        </div>
                        <p class="text-xs sm:text-sm leading-5 text-gray-300">Hi! Ask me about neighbourhoods, pricing, wifi or house rules for any verified stay.</p>
                    </div>

                    <div class="mt-3 space-y-2 max-h-36 overflow-y-auto" aria-live="polite">
                        <template x-for="message in messages" :key="message.id">
                            <p class="rounded-xl px-3 py-2 text-xs leading-5" :class="message.type === 'user' ? 'ml-10 bg-orange-500 text-white' : 'mr-10 bg-gray-700 text-gray-200'" x-text="message.text"></p>
                        </template>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="button" @click="sendQuickMessage('Beachfront under ₦100k?')" class="rounded-full border border-white/10 bg-gray-700 px-3 py-1.5 text-[11px] font-semibold text-gray-200 hover:border-orange-400">Beachfront under ₦100k?</button>
                        <button type="button" @click="sendQuickMessage('Stay for business?')" class="rounded-full border border-white/10 bg-gray-700 px-3 py-1.5 text-[11px] font-semibold text-gray-200 hover:border-orange-400">Stay for business?</button>
                    </div>

                    <form @submit.prevent="sendMessage" class="mt-3 flex gap-2">
                        <input x-model="inputText" type="text" placeholder="Ask about any verified stay..." class="min-w-0 flex-1 rounded-xl border border-white/10 bg-gray-700 px-3.5 py-2.5 text-sm text-white placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500">
                        <button type="submit" aria-label="Send question" class="h-10 w-10 shrink-0 rounded-xl bg-orange-500 text-white hover:bg-orange-600 flex items-center justify-center">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 14-7-4 14-3-6-7-1Z"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <div class="divide-y divide-white/10 border-y border-white/10">
                @foreach([
                    ['GUEST MATCHING', 'Suggests stays based on what you ask', 'Mention “remote work” or “family trip” to narrow the list to stays that genuinely fit.'],
                    ['PLAIN-LANGUAGE Q&A', 'Answers from verified listing data', 'Get concise answers about house rules, wifi and the details that have actually been checked.'],
                    ['FOR HOSTS', 'Pricing suggestions while you list', 'Compare nearby verified stays to guide a practical nightly rate.'],
                ] as [$label, $heading, $copy])
                    <article class="py-5 first:pt-1 last:pb-1">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-orange-500">{{ $label }}</p>
                        <h3 class="mt-1.5 text-base font-bold text-white">{{ $heading }}</h3>
                        <p class="mt-1 text-xs sm:text-sm leading-5 text-gray-400">{{ $copy }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function aiConcierge() {
    return {
        inputText: '', messages: [], messageId: 0,
        sendMessage() {
            if (!this.inputText.trim()) return;
            const question = this.inputText;
            this.messages.push({ id: this.messageId++, type: 'user', text: question });
            this.inputText = '';
            setTimeout(() => this.messages.push({ id: this.messageId++, type: 'bot', text: this.getAIResponse(question) }), 350);
        },
        sendQuickMessage(text) { this.inputText = text; this.sendMessage(); },
        getAIResponse(question) {
            const value = question.toLowerCase();
            if (value.includes('beachfront') || value.includes('100k')) return 'Try the beachfront stays in Lekki and Victoria Island, then set your maximum nightly price to ₦100k.';
            if (value.includes('business')) return 'Choose Business stays for serviced apartments with reliable power, wifi and easy access to Lagos commercial districts.';
            return 'Tell me your location, budget, dates or trip type and I’ll help narrow the verified stays.';
        }
    }
}
</script>
@endpush
