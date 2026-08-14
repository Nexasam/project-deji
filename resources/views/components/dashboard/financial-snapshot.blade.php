<div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h3 class="text-base font-bold text-gray-900 mb-0.5">Financial snapshot</h3>
            <p class="text-xs text-gray-500">Payouts collected across all listings</p>
        </div>
        <div class="flex bg-gray-100 rounded-lg p-0.5">
            <button class="px-3 py-1 text-xs font-medium bg-white rounded shadow-sm text-gray-900">30d</button>
            <button class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900">90d</button>
            <button class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900">1y</button>
        </div>
    </div>

    {{-- Chart --}}
    <div class="relative h-48 mb-4" x-data="financialChart()">
        <canvas id="financialChart" class="w-full h-full"></canvas>
    </div>

    {{-- Legend --}}
    <div class="flex items-center justify-center gap-6 text-xs">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-[#FF5A00] rounded-sm"></div>
            <span class="text-gray-600">Revenue</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-gray-900 rounded-sm"></div>
            <span class="text-gray-600">Expenses</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
function financialChart() {
    return {
        init() {
            const canvas = document.getElementById('financialChart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            const width = canvas.offsetWidth;
            const height = canvas.offsetHeight;
            canvas.width = width;
            canvas.height = height;
            
            // Mock data points
            const revenueData = [1.2, 1.5, 1.3, 1.8, 2.0, 1.9, 2.2, 2.4, 2.3, 2.6, 2.5, 2.7];
            const expenseData = [0.8, 1.0, 0.9, 1.2, 1.3, 1.2, 1.4, 1.5, 1.4, 1.6, 1.5, 1.7];
            
            const maxValue = Math.max(...revenueData, ...expenseData) * 1.1;
            const padding = 30;
            const chartWidth = width - padding * 2;
            const chartHeight = height - padding * 2;
            
            // Draw revenue area
            ctx.fillStyle = 'rgba(255, 90, 0, 0.1)';
            ctx.beginPath();
            ctx.moveTo(padding, height - padding);
            revenueData.forEach((value, i) => {
                const x = padding + (i / (revenueData.length - 1)) * chartWidth;
                const y = height - padding - (value / maxValue) * chartHeight;
                if (i === 0) ctx.lineTo(x, y);
                else ctx.lineTo(x, y);
            });
            ctx.lineTo(width - padding, height - padding);
            ctx.closePath();
            ctx.fill();
            
            // Draw revenue line
            ctx.strokeStyle = '#FF5A00';
            ctx.lineWidth = 2;
            ctx.beginPath();
            revenueData.forEach((value, i) => {
                const x = padding + (i / (revenueData.length - 1)) * chartWidth;
                const y = height - padding - (value / maxValue) * chartHeight;
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            });
            ctx.stroke();
            
            // Draw expense line
            ctx.strokeStyle = '#222222';
            ctx.lineWidth = 2;
            ctx.beginPath();
            expenseData.forEach((value, i) => {
                const x = padding + (i / (expenseData.length - 1)) * chartWidth;
                const y = height - padding - (value / maxValue) * chartHeight;
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            });
            ctx.stroke();
        }
    }
}
</script>
@endpush
