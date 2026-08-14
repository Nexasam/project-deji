<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 7 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ 
    documents: [
        { name: 'Egbeda Properties Bluewater Tenancy Agreement.pdf', size: '30 KB' },
        { name: 'Egbeda Properties Bluewater Rules and Regulations.pdf', size: '30 KB' },
        { name: 'Egbeda Properties Bluewater Tenancy Agreement.pdf', size: '30 KB' }
    ]
}">
    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-8 w-auto" />
                    <span class="text-gray-900 text-lg font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">Step 7 of 9</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</button>
                </div>
            </div>
            <div class="h-1 bg-gray-200"><div class="h-1 bg-[#FF5A00]" style="width: 77.77%"></div></div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-12">
            <div class="w-full max-w-4xl mx-auto">
                {{-- Title Section --}}
                <div class="mb-8">
                    <p class="text-[#FF5A00] text-sm font-bold uppercase tracking-wider mb-2">PAPERWORK</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Any documents to keep on file?</h1>
                    <p class="text-gray-600 text-base">
                        Totally optional — lease agreements, inspection reports or receipts. Whatever you upload here stays attached to this property for your own records.
                    </p>
                </div>

                {{-- Upload Area --}}
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                    {{-- Dashed Upload Box --}}
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-16 mb-6 text-center hover:border-[#FF5A00] transition-colors cursor-pointer">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-[#FF5A00] rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-gray-900 text-base mb-1">
                                <span class="font-bold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-gray-500 text-sm">PDF, DOC or image files, up to 10</p>
                        </div>
                    </div>

                    {{-- Uploaded Documents List --}}
                    <div class="space-y-3">
                        <template x-for="(doc, index) in documents" :key="index">
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                                            <path d="M14 2v6h6"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-900 text-sm font-medium" x-text="doc.name"></span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-gray-600 text-sm" x-text="doc.size"></span>
                                    <button @click="documents.splice(index, 1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="/property/add/step6" class="text-gray-600 font-medium text-base hover:text-gray-900 transition-colors underline">Back</a>
                    <button class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-3 px-10 rounded-lg text-base transition-colors shadow-lg">Next Step</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
