<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Listing Submitted - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-7 w-auto" />
                    <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">Step 9 of 9</span>
                    <a href="/owner/properties"
                       class="px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Save & exit
                    </a>
                </div>
            </div>
            <div class="h-0.5 bg-[#FF5A00]"></div>
        </header>

        {{-- Main --}}
        <main class="flex-1 flex items-center justify-center px-4 py-10">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm w-full max-w-lg px-10 py-12 text-center">

                {{-- Hourglass icon --}}
                <div class="flex justify-center mb-6">
                    <svg width="70" height="93" viewBox="0 0 139 185" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M130.312 0H8.6875C3.88947 0 0 3.88247 0 8.67188V14.4531C0 19.2425 3.88947 23.125 8.6875 23.125C8.6875 55.9932 27.1542 83.732 52.4298 92.5C27.1542 101.268 8.6875 129.007 8.6875 161.875C3.88947 161.875 0 165.757 0 170.547V176.328C0 181.118 3.88947 185 8.6875 185H130.312C135.111 185 139 181.118 139 176.328V170.547C139 165.757 135.111 161.875 130.312 161.875C130.312 129.007 111.846 101.268 86.5702 92.5C111.846 83.732 130.312 55.9932 130.312 23.125C135.111 23.125 139 19.2425 139 14.4531V8.67188C139 3.88247 135.111 0 130.312 0ZM107.146 161.875H31.8542C31.8542 133.887 48.5791 109.844 69.5 109.844C90.4173 109.844 107.146 133.878 107.146 161.875Z" fill="#0F0F0F"/>
                    </svg>
                </div>
                

                <h1 class="text-2xl font-bold text-gray-900 mb-3">Pending approval</h1>
                <p class="text-sm text-gray-500 leading-relaxed max-w-sm mx-auto mb-8">
                    Your listing has been submitted — nice work! We're verifying the details
                    and photos now, which usually takes less than 24 hours. Once it's
                    approved, it'll go live on your dashboard and the Verified Shortlet
                    marketplace.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="/owner/properties"
                       class="w-full sm:w-auto px-8 py-3 border-2 border-gray-900 text-gray-900 font-bold text-sm rounded-xl hover:bg-gray-50 transition-colors">
                        Back to Properties
                    </a>
                    <a href="/owner/properties/create/step1"
                       class="w-full sm:w-auto px-8 py-3 bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold text-sm rounded-xl transition-colors">
                        Add another property
                    </a>
                </div>

            </div>
        </main>

    </div>
</body>
</html>
