<?php
// Request New Event - Student page (Epic E4 - Authorization Form)
session_start();
require_once 'config/db.php';

$page_title = "Request New Event";
require_once 'includes/student_header.php';

// Check karo ke system active hai
if (!$system_active) {
    $_SESSION['error'] = "System is currently inactive. Event submissions are disabled.";
    header("Location: student_dashboard.php");
    exit();
}
?>

<!-- Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<form action="process_event.php" method="POST" id="eventForm" class="space-y-6">
    <!-- Event Details Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Event Details
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                <input type="text" name="title" required maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                    placeholder="Enter event title">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <textarea name="description" required rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                    placeholder="Describe your event in detail"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expected Date *</label>
                <input type="date" name="expected_date" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                    min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Venue *</label>
                <input type="text" name="venue" required maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                    placeholder="Event venue/location">
            </div>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Team Members (Optional)
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Team Member 1 (Reg ID)</label>
                <input type="text" name="team_member_1" maxlength="50"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                    placeholder="e.g., STU-001">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Team Member 2 (Reg ID)</label>
                <input type="text" name="team_member_2" maxlength="50"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                    placeholder="e.g., STU-002">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Team Member 3 (Reg ID)</label>
                <input type="text" name="team_member_3" maxlength="50"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                    placeholder="e.g., STU-003">
            </div>
        </div>
    </div>

    <!-- Itemized Budget Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Itemized Budget
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full" id="budgetTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Unit Rate (PKR)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Total (PKR)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Action</th>
                    </tr>
                </thead>
                <tbody id="budgetItems">
                    <tr class="budget-row border-b">
                        <td class="px-4 py-3">
                            <input type="text" name="items[0][name]" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                                placeholder="Item name">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="items[0][quantity]" min="1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent qty-input"
                                placeholder="0" onchange="calculateRow(this)">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="items[0][rate]" min="0" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent rate-input"
                                placeholder="0.00" onchange="calculateRow(this)">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="items[0][total]" readonly
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg total-input"
                                placeholder="0.00">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700" title="Remove">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
            <button type="button" onclick="addRow()" class="flex items-center text-cause-purple hover:text-cause-purple-dark font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>
            
            <div class="text-right">
                <span class="text-gray-600 font-medium">Grand Total: </span>
                <span class="text-2xl font-bold text-cause-purple" id="grandTotal">PKR 0.00</span>
                <input type="hidden" name="grand_total" id="grandTotalInput" value="0">
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end space-x-4">
        <a href="student_dashboard.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Cancel
        </a>
        <button type="submit" class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Submit Event Request
        </button>
    </div>
</form>

<script>
// Row counter for unique names
let rowCount = 1;

// Naya row add karo
function addRow() {
    const tbody = document.getElementById('budgetItems');
    const newRow = document.createElement('tr');
    newRow.className = 'budget-row border-b';
    newRow.innerHTML = `
        <td class="px-4 py-3">
            <input type="text" name="items[${rowCount}][name]" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                placeholder="Item name">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[${rowCount}][quantity]" min="1" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent qty-input"
                placeholder="0" onchange="calculateRow(this)">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[${rowCount}][rate]" min="0" step="0.01" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent rate-input"
                placeholder="0.00" onchange="calculateRow(this)">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[${rowCount}][total]" readonly
                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg total-input"
                placeholder="0.00">
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700" title="Remove">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    rowCount++;
}

// Row remove karo
function removeRow(btn) {
    const rows = document.querySelectorAll('.budget-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
        calculateGrandTotal();
    } else {
        alert('At least one item is required!');
    }
}

// Row ka total calculate karo
function calculateRow(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const total = qty * rate;
    row.querySelector('.total-input').value = total.toFixed(2);
    calculateGrandTotal();
}

// Grand total calculate karo
function calculateGrandTotal() {
    const totals = document.querySelectorAll('.total-input');
    let grandTotal = 0;
    totals.forEach(input => {
        grandTotal += parseFloat(input.value) || 0;
    });
    document.getElementById('grandTotal').textContent = 'PKR ' + grandTotal.toFixed(2);
    document.getElementById('grandTotalInput').value = grandTotal.toFixed(2);
}
</script>

<?php require_once 'includes/student_footer.php'; ?>
