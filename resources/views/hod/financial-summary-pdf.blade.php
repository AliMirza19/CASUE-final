<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Summary - {{ $activeTerm ? $activeTerm->term_name : 'Current Term' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #8B5CF6;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .header-table {
            width: 100%;
            border: none;
            margin-bottom: 10px;
        }
        .header-table td {
            border: none;
            vertical-align: middle;
        }
        .header h1 {
            color: #8B5CF6;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 12px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .metric-card h3 {
            margin: 0 0 10px 0;
            color: #8B5CF6;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .metric-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #8B5CF6;
            border-bottom: 1px solid #8B5CF6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #8B5CF6;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .alert-danger {
            background-color: #fef2f2;
            border-left-color: #ef4444;
            color: #dc2626;
        }
        .alert-warning {
            background-color: #fffbeb;
            border-left-color: #f59e0b;
            color: #d97706;
        }
        .alert-success {
            background-color: #f0fdf4;
            border-left-color: #10b981;
            color: #059669;
        }
        .alert-info {
            background-color: #eff6ff;
            border-left-color: #3b82f6;
            color: #2563eb;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .header {
                page-break-after: avoid;
            }
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 20%; text-align: left;">
                    <img src="https://admission.cust.edu.pk/web/image/website/1/logo?unique=f3e0a29" style="height: 70px; width: auto;">
                </td>
                <td style="width: 60%; text-align: center;">
                    <h1>CAUSE Society Financial Summary</h1>
                    <p><strong>Term:</strong> {{ $activeTerm ? $activeTerm->term_name : 'Current Term' }}</p>
                    <p><strong>Generated:</strong> {{ now()->format('F d, Y \a\t g:i A') }}</p>
                </td>
                <td style="width: 20%; text-align: right;">
                    @php
                        $logoPath = public_path('images/cause-logo.png');
                        $logoData = '';
                        if (file_exists($logoPath)) {
                            $logoData = base64_encode(file_get_contents($logoPath));
                        }
                    @endphp
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" style="height: 70px; width: auto;">
                    @endif
                </td>
            </tr>
        </table>
        <p style="font-size: 11px; color: #888;">Capital University of Science & Technology - CAUSE Smart Management System</p>
    </div>

    <!-- Budget Alerts -->
    @if(isset($financialReport['alerts']) && count($financialReport['alerts']) > 0)
        <div class="section">
            <h2>Budget Alerts</h2>
            @foreach($financialReport['alerts'] as $alert)
                <div class="alert alert-{{ $alert['type'] }}">
                    <strong>{{ ucfirst($alert['type']) }}:</strong> {{ $alert['message'] }}
                </div>
            @endforeach
        </div>
    @endif

    <!-- Financial Metrics -->
    <div class="section">
        <h2>Financial Overview</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <h3>Total Budget</h3>
                <p class="value">Rs. {{ number_format($financialReport['summary']['budget_overview']['total_allocation'] ?? 0, 2) }}</p>
            </div>
            <div class="metric-card">
                <h3>Total Spent</h3>
                <p class="value">Rs. {{ number_format($financialReport['summary']['budget_overview']['total_spent'] ?? 0, 2) }}</p>
            </div>
            <div class="metric-card">
                <h3>Remaining Balance</h3>
                <p class="value">Rs. {{ number_format($financialReport['summary']['budget_overview']['remaining_balance'] ?? 0, 2) }}</p>
            </div>
            <div class="metric-card">
                <h3>Budget Utilization</h3>
                <p class="value">{{ number_format($financialReport['summary']['budget_overview']['spent_percentage'] ?? 0, 1) }}%</p>
            </div>
        </div>
    </div>

    <!-- Event Statistics -->
    <div class="section">
        <h2>Event Statistics</h2>
        <table class="table">
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Approved Events</td>
                <td>{{ $financialReport['summary']['spending_breakdown']['approved_events_count'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Pending Events</td>
                <td>{{ $financialReport['summary']['spending_breakdown']['pending_events_count'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Average Event Cost</td>
                <td>Rs. {{ number_format($financialReport['summary']['spending_breakdown']['average_event_cost'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Highest Event Cost</td>
                <td>Rs. {{ number_format($financialReport['summary']['spending_breakdown']['highest_event_cost'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Lowest Event Cost</td>
                <td>Rs. {{ number_format($financialReport['summary']['spending_breakdown']['lowest_event_cost'] ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Top Spending Events -->
    <div class="section">
        <h2>Top 5 Most Expensive Events</h2>
        @if(isset($financialReport['summary']['top_events']) && count($financialReport['summary']['top_events']) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Student</th>
                        <th>Cost</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($financialReport['summary']['top_events'] as $event)
                        <tr>
                            <td>{{ $event['title'] }}</td>
                            <td>{{ $event['student'] }}</td>
                            <td>Rs. {{ number_format($event['cost'], 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($event['date'])->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                No approved events found for this term.
            </div>
        @endif
    </div>

    <!-- Term Information -->
    <div class="section">
        <h2>Term Information</h2>
        <table class="table">
            <tr>
                <th>Term Name</th>
                <td>{{ $financialReport['summary']['term_info']['name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{ $financialReport['summary']['term_info']['start_date'] ? \Carbon\Carbon::parse($financialReport['summary']['term_info']['start_date'])->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>{{ $financialReport['summary']['term_info']['end_date'] ? \Carbon\Carbon::parse($financialReport['summary']['term_info']['end_date'])->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $financialReport['summary']['term_info']['is_active'] ? 'Active' : 'Inactive' }}</td>
            </tr>
        </table>
    </div>

    <!-- Budget Analysis -->
    <div class="section">
        <h2>Budget Analysis</h2>
        <p><strong>Budget Health:</strong> 
            @if(($financialReport['summary']['budget_overview']['spent_percentage'] ?? 0) < 70)
                <span style="color: #10b981;">Healthy - Good budget management</span>
            @elseif(($financialReport['summary']['budget_overview']['spent_percentage'] ?? 0) < 85)
                <span style="color: #f59e0b;">Caution - Monitor spending closely</span>
            @else
                <span style="color: #ef4444;">Critical - Budget nearly exhausted</span>
            @endif
        </p>
        
        <p><strong>Spending Efficiency:</strong> {{ $financialReport['spending_efficiency'] ?? 0 }}%</p>
        
        <p><strong>Recommendations:</strong></p>
        <ul>
            @if(($financialReport['summary']['budget_overview']['spent_percentage'] ?? 0) > 80)
                <li>Consider reviewing pending event approvals carefully</li>
                <li>Monitor remaining budget closely for future events</li>
            @endif
            @if(($financialReport['summary']['spending_breakdown']['pending_events_count'] ?? 0) > 0)
                <li>{{ $financialReport['summary']['spending_breakdown']['pending_events_count'] }} events are pending approval</li>
            @endif
            @if(($financialReport['summary']['budget_overview']['remaining_balance'] ?? 0) > 0)
                <li>Rs. {{ number_format($financialReport['summary']['budget_overview']['remaining_balance'], 2) }} available for future events</li>
            @endif
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by the CAUSE Society Management System.</p>
        <p>For questions or concerns, please contact the system administrator.</p>
        <p><strong>Generated on:</strong> {{ now()->format('F d, Y \a\t g:i:s A') }}</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>