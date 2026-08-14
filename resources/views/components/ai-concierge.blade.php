@props([
    'title' => 'Ask before you book, not after',
    'subtitle' => 'AI TRIP CONCIERGE',
    'description' => 'Type a question about any neighbourhood, budget or house rule and get an answer grounded in verified listing data.'
])

<section class="bg-gray-900 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="mb-12">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-wider mb-3">{{ $subtitle }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">{{ $title }}</h2>
            <p class="text-gray-400 text-base max-w-2xl">{{ $description }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Chat Interface --}}
            <div class="bg-gray-800 rounded-2xl p-6" x-data="aiConcierge()">
                {{-- Chat Header --}}
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-white font-bold">AI Trip Concierge</span>
                </div>

                {{-- Chat Messages --}}
                <div class="space-y-4 mb-6 min-h-[200px]">
                    {{-- Initial Bot Message --}}
                    <div class="flex gap-3">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="bg-gray-700 rounded-2xl rounded-tl-sm px-4 py-3 max-w-md">
                            <p class="text-sm text-gray-200">Hi! I'm the Verified Shortlet concierge. Ask me about neighbourhoods, pricing, wifi or house rules for any verified stay.</p>
                        </div>
                    </div>

                    {{-- User Messages (Alpine.js) --}}
                    <template x-for="message in messages" :key="message.id">
                        <div>
                            <div x-show="message.type === 'user'" class="flex gap-3 justify-end">
                                <div class="bg-orange-500 rounded-2xl rounded-tr-sm px-4 py-3 max-w-md">
                                    <p class="text-sm text-white" x-text="message.text"></p>
                                </div>
                            </div>
                            <div x-show="message.type === 'bot'" class="flex gap-3">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="bg-gray-700 rounded-2xl rounded-tl-sm px-4 py-3 max-w-md">
                                    <p class="text-sm text-gray-200" x-text="message.text"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Quick Action Pills --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <button @click="sendQuickMessage('Beachfront under ₦100k?')" class="text-xs bg-gray-700 hover:bg-gray-600 text-gray-300 px-3 py-1.5 rounded-full transition-colors">
                        Beachfront under ₦100k?
                    </button>
                    <button @click="sendQuickMessage('Stay for business?')" class="text-xs bg-gray-700 hover:bg-gray-600 text-gray-300 px-3 py-1.5 rounded-full transition-colors">
                        Stay for business?
                    </button>
                </div>

                {{-- Input Form --}}
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input 
                        x-model="inputText"
                        type="text" 
                        placeholder="Ask about any verified stay..."
                        class="flex-1 bg-gray-700 text-white placeholder-gray-400 px-4 py-3 rounded-full text-sm outline-none focus:ring-2 focus:ring-orange-500"
                    />
                    <button 
                        type="submit"
                        class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-full flex items-center justify-center transition-colors flex-shrink-0"
                    >
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Feature Cards --}}
            <div class="space-y-4">
                {{-- Guest Matching --}}
                <div class="bg-gray-800 rounded-2xl p-6">
                    <p class="text-xs font-bold text-orange-500 uppercase tracking-wider mb-2">GUEST MATCHING</p>
                    <h3 class="text-xl font-bold text-white mb-3">Suggests stays based on what you ask</h3>
                    <p class="text-sm text-gray-400">Mention "remote work" or "family trip" and the concierge narrows the list to stays that fit, not just what's available.</p>
                </div>

                {{-- Plain Language Q&A --}}
                <div class="bg-gray-800 rounded-2xl p-6">
                    <p class="text-xs font-bold text-orange-500 uppercase tracking-wider mb-2">PLAIN-LANGUAGE Q&A</p>
                    <h3 class="text-xl font-bold text-white mb-3">Answers pulled from verified listing data</h3>
                    <p class="text-sm text-gray-400">House rules, wifi speed, host response time — the concierge only speaks from what's actually been checked.</p>
                </div>

                {{-- For Hosts --}}
                <div class="bg-gray-800 rounded-2xl p-6">
                    <p class="text-xs font-bold text-orange-500 uppercase tracking-wider mb-2">FOR HOSTS</p>
                    <h3 class="text-xl font-bold text-white mb-3">Pricing suggestions while you list</h3>
                    <p class="text-sm text-gray-400">When you list a property, the same AI layer suggests a nightly rate based on comparable verified stays nearby.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function aiConcierge() {
    return {
        inputText: '',
        messages: [],
        messageId: 0,

        sendMessage() {
            if (!this.inputText.trim()) return;

            // Add user message
            this.messages.push({
                id: this.messageId++,
                type: 'user',
                text: this.inputText
            });

            const userQuestion = this.inputText;
            this.inputText = '';

            // Simulate AI response
            setTimeout(() => {
                this.messages.push({
                    id: this.messageId++,
                    type: 'bot',
                    text: this.getAIResponse(userQuestion)
                });
            }, 1000);
        },

        sendQuickMessage(text) {
            this.inputText = text;
            this.sendMessage();
        },

        getAIResponse(question) {
            const responses = {
                'beachfront': 'I found 12 verified beachfront properties under ₦100k/night in Lekki and Victoria Island. The average wifi speed is 50Mbps, and all have 24/7 security.',
                'business': 'For business stays, I recommend properties in Ikoyi and VI with dedicated workspaces, high-speed wifi (100Mbps+), and proximity to business districts. 8 verified options available.',
                'default': 'Based on verified listing data, I can help you find properties that match your specific needs. Could you tell me more about your preferences - location, budget, or special requirements?'
            };

            const lowerQuestion = question.toLowerCase();
            if (lowerQuestion.includes('beachfront') || lowerQuestion.includes('100k')) {
                return responses.beachfront;
            } else if (lowerQuestion.includes('business')) {
                return responses.business;
            } else {
                return responses.default;
            }
        }
    }
}
</script>
@endpush
