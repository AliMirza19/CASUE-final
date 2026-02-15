<?php
// HOD Analytics - Epic E10 (Financial Reports)
session_start();
require_once 'config/db.php';

$page_title = "Financial Analytics & Reports";
require_once 'includes/hod_header.php';

// Analytics data fetch karo
try {
    // Current term budget info
    $stmt = $pdo->prepare("SELECT total_amount, remaining_amount FROM budgets WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $budget_info = $stmt->fetch();
    
    $total_budget = $budget_info ? $budget_info['total_amount'] : 0;
    $remaining_budget = $budget_info ? $budget_info['remaining_amount'] : 0;
    $spent_budget = $total_budget - $remaining_budget;
    
    // Top 5 most expensive events
    $stmt = $pdo->prepare("SELECT e.title, e.grand_total, e.expected_date, u.name as student_name, u.reg_id as student_reg_id
                           FROM events e
                           JOIN users u ON e.student_id = u.id
                           WHERE e.term_id = :term_id AND e.status IN ('approved', 'completed')
                           ORDER BY e.grand_total DESC
                           LIMIT 5");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $top_events = $stmt->fetchAll();
    
    // Events breakdown by category (based on title keywords)
    $stmt = $pdo->prepare("SELECT 
                               CASE 
                                   WHEN LOWER(title) LIKE '%seminar%' THEN 'Seminars'
                                   WHEN LOWER(title) LIKE '%workshop%' THEN 'Workshops'
                                   WHEN LOWER(title) LIKE '%conference%' THEN 'Conferences'
                                   WHEN LOWER(title) LIKE '%competition%' THEN 'Competitions'
                                   WHEN LOWER(title) LIKE '%cultural%' OR LOWER(title) LIKE '%fest%' THEN 'Cultural Events'
                                   WHEN LOWER(title) LIKE '%sports%' THEN 'Sports Events'
                                   ELSE 'Other Events'
                               END as category,
                               SUM(grand_total) as total_spent,
                               COUNT(*) as event_count
                           FROM events 
                           WHERE term_id = :term_id AND status IN ('approved', 'completed')
                           GROUP BY category
                           ORDER BY total_spent DESC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $category_breakdown = $stmt->fetchAll();
    
    // Monthly spending trend
    $stmt = $pdo->prepare("SELECT 
                               DATE_FORMAT(updated_at, '%Y-%m') as month,
                               SUM(grand_total) as monthly_spent,
                               COUNT(*) as events_count
                           FROM events 
                           WHERE term_id = :term_id AND status IN ('approved', 'completed')
                           GROUP BY month
                           ORDER BY month");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $monthly_trend = $stmt->fetchAll();
    
    // Overall statistics
    $stmt = $pdo->prepare("SELECT 
                               COUNT(*) as total_events,
                               COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_events,
                               COUNT(CASE WHEN status = 'pending_president' THEN 1 END) as pending_events,
                               COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_events,
                               AVG(grand_total) as avg_event_cost
                           FROM events 
                           WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $stats = $stmt->fetch();
    
} catch(PDOException $e) {
    $total_budget = 0;
    $spent_budget = 0;
    $remaining_budget = 0;
    $top_events = [];
    $category_breakdown = [];
    $monthly_trend = [];
    $stats = ['total_events' => 0, 'approved_events' => 0, 'pending_events' => 0, 'rejected_events' => 0, 'avg_event_cost' => 0];
    $_SESSION['error'] = "Error loading analytics data!";
}
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Dashboard Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">PKR <?php echo number_format($total_budget, 0); ?></p>
                <p class="text-gray-600">Total Budget</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">PKR <?php echo number_format($spent_budget, 0); ?></p>
                <p class="text-gray-600">Total Spent</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo $stats['approved_events']; ?></p>
                <p class="text-gray-600">Approved Events</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">PKR <?php echo number_format($stats['avg_event_cost'], 0); ?></p>
                <p class="text-gray-600">Avg Event Cost</p>
            </div>
        </div>
    </div>
</div>

<!-- Print Report Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8 print:hidden">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Financial Reports</h3>
            <p class="text-gray-600 text-sm">Generate printable reports for documentation</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="printBudgetSummary()" 
                    class="bg-cause-purple hover:bg-cause-purple-dark text-white px-4 py-2 rounded-lg font-medium transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Budget Summary
            </button>
            <button onclick="printEventsList()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Events Report
            </button>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Budget Overview Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Budget Overview</h3>
        <div class="relative h-64">
            <canvas id="budgetChart"></canvas>
        </div>
    </div>
    
    <!-- Events by Category Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Spending by Event Category</h3>
        <div class="relative h-64">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<!-- Monthly Trend Chart -->
<?php if (!empty($monthly_trend)): ?>
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Spending Trend</h3>
    <div class="relative h-64">
        <canvas id="trendChart"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- Top Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Top 5 Most Expensive Events</h3>
        <p class="text-gray-600 text-sm mt-1">Highest budget events approved this term</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organizer</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Budget</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% of Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($top_events)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-gray-500">No approved events found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($top_events as $index => $event): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-cause-purple text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        <?php echo $index + 1; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['student_reg_id']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                <?php echo date('M d, Y', strtotime($event['expected_date'])); ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-semibold text-gray-800">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-gray-600">
                                    <?php echo $spent_budget > 0 ? number_format(($event['grand_total'] / $spent_budget) * 100, 1) : 0; ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js Scripts -->
<script>
// Budget Overview Chart (Bar Chart)
const budgetCtx = document.getElementById('budgetChart').getContext('2d');
new Chart(budgetCtx, {
    type: 'bar',
    data: {
        labels: ['Total Budget', 'Spent', 'Remaining'],
        datasets: [{
            label: 'Amount (PKR)',
            data: [<?php echo $total_budget; ?>, <?php echo $spent_budget; ?>, <?php echo $remaining_budget; ?>],
            backgroundColor: [
                '#7C3AED',
                '#DC2626', 
                '#059669'
            ],
            borderColor: [
                '#5B21B6',
                '#B91C1C',
                '#047857'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'PKR ' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Events by Category Chart (Pie Chart)
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: [<?php echo implode(',', array_map(function($cat) { return "'" . $cat['category'] . "'"; }, $category_breakdown)); ?>],
        datasets: [{
            data: [<?php echo implode(',', array_map(function($cat) { return $cat['total_spent']; }, $category_breakdown)); ?>],
            backgroundColor: [
                '#7C3AED',
                '#DC2626',
                '#059669',
                '#F59E0B',
                '#3B82F6',
                '#8B5CF6',
                '#10B981'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

<?php if (!empty($monthly_trend)): ?>
// Monthly Trend Chart (Line Chart)
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: [<?php echo implode(',', array_map(function($month) { return "'" . date('M Y', strtotime($month['month'] . '-01')) . "'"; }, $monthly_trend)); ?>],
        datasets: [{
            label: 'Monthly Spending (PKR)',
            data: [<?php echo implode(',', array_map(function($month) { return $month['monthly_spent']; }, $monthly_trend)); ?>],
            borderColor: '#7C3AED',
            backgroundColor: 'rgba(124, 58, 237, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'PKR ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
<?php endif; ?>

// Print Functions
function printBudgetSummary() {
    const printWindow = window.open('', '_blank');
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Budget Summary Report - CAUSE</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #7C3AED; padding-bottom: 20px; }
                .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
                .stat-box { border: 1px solid #ddd; padding: 15px; border-radius: 8px; }
                .stat-value { font-size: 24px; font-weight: bold; color: #7C3AED; }
                .stat-label { color: #666; margin-top: 5px; }
                .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>CAUSE Society - Budget Summary Report</h1>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
                <p>Academic Term: Fall 2024</p>
            </div>
            
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-value">PKR ${<?php echo number_format($total_budget, 0); ?>}</div>
                    <div class="stat-label">Total Budget Allocated</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">PKR ${<?php echo number_format($spent_budget, 0); ?>}</div>
                    <div class="stat-label">Total Amount Spent</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">PKR ${<?php echo number_format($remaining_budget, 0); ?>}</div>
                    <div class="stat-label">Remaining Balance</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">${<?php echo $stats['approved_events']; ?>}</div>
                    <div class="stat-label">Approved Events</div>
                </div>
            </div>
            
            <div class="footer">
                <p>CAUSE Smart Society Management System</p>
                <p>This report was generated automatically by the system</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

function printEventsList() {
    const printWindow = window.open('', '_blank');
    const eventsData = <?php echo json_encode($top_events); ?>;
    
    let eventsTable = '';
    eventsData.forEach((event, index) => {
        eventsTable += `
            <tr>
                <td>${index + 1}</td>
                <td>${event.title}</td>
                <td>${event.student_name}</td>
                <td>${new Date(event.expected_date).toLocaleDateString()}</td>
                <td>PKR ${parseFloat(event.grand_total).toLocaleString()}</td>
            </tr>
        `;
    });
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Events Report - CAUSE</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #7C3AED; padding-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #7C3AED; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>CAUSE Society - Events Report</h1>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
                <p>Academic Term: Fall 2024</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Event Title</th>
                        <th>Organizer</th>
                        <th>Date</th>
                        <th>Budget</th>
                    </tr>
                </thead>
                <tbody>
                    ${eventsTable}
                </tbody>
            </table>
            
            <div class="footer">
                <p>CAUSE Smart Society Management System</p>
                <p>This report was generated automatically by the system</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}
</script>

<?php require_once 'includes/hod_footer.php'; ?>