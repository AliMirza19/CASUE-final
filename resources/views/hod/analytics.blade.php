@extends('layouts.dashboard')

@section('title', 'Analytics - CAUSE Smart Society')
@section('page-title', 'Financial Analytics')
@section('page-description', 'Budget and event spending analysis')

@section('sidebar')
    <a href="{{ route('hod.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('hod.manage-patron') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Manage Patron
    </a>
    <a href="{{ route('hod.budget') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Manage Budget
    </a>
    <a href="{{ route('hod.analytics') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Analytics
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Term Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-600 font-medium">Current Term</p>
                <p class="text-lg font-bold text-blue-800">{{ $currentTerm ? $currentTerm->term_name : 'No Term' }}</p>
            </div>
        </div>
    </div>

    <!-- Budget Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
            <p class="text-gray-600 text-sm font-medium">Total Budget</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">PKR {{ number_format($totalBudget, 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-medium">Remaining</p>
            <p class="text-2xl font-bold text-green-600 mt-2">PKR {{ number_format($remainingBudget, 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <p class="text-gray-600 text-sm font-medium">Spent</p>
            <p class="text-2xl font-bold text-red-600 mt-2">PKR {{ number_format($spentBudget, 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Budget Pie Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Budget Distribution</h3>
            <div class="relative">
                <canvas id="budgetPieChart" height="250"></canvas>
            </div>
            <div class="mt-4 flex justify-center space-x-6">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Remaining ({{ $totalBudget > 0 ? round(($remainingBudget / $totalBudget) * 100) : 0 }}%)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Spent ({{ $totalBudget > 0 ? round(($spentBudget / $totalBudget) * 100) : 0 }}%)</span>
                </div>
            </div>
        </div>

        <!-- Events by Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Events by Status</h3>
            <div class="relative">
                <canvas id="eventsBarChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Events by Budget -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Top Events by Budget</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% of Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($topEvents as $index => $event)
                    <tr>
                        <td class="px-6 py-4 text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $event->title }}</div>
                            <div class="text-sm text-gray-500">{{ $event->venue }}</div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">PKR {{ number_format($event->grand_total, 0) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-cause-purple h-2 rounded-full" style="width: {{ $totalBudget > 0 ? min(($event->grand_total / $totalBudget) * 100, 100) : 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $totalBudget > 0 ? round(($event->grand_total / $totalBudget) * 100, 1) : 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No approved events yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Budget Pie Chart
const budgetCtx = document.getElementById('budgetPieChart').getContext('2d');
new Chart(budgetCtx, {
    type: 'doughnut',
    data: {
        labels: ['Remaining', 'Spent'],
        datasets: [{
            data: [{{ $remainingBudget }}, {{ $spentBudget }}],
            backgroundColor: ['#22c55e', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '60%'
    }
});

// Events Bar Chart
const eventsCtx = document.getElementById('eventsBarChart').getContext('2d');
new Chart(eventsCtx, {
    type: 'bar',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            label: 'Events',
            data: [{{ $eventsByStatus['pending'] }}, {{ $eventsByStatus['approved'] }}, {{ $eventsByStatus['rejected'] }}],
            backgroundColor: ['#eab308', '#22c55e', '#ef4444'],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush
