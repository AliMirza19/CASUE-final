<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAUSE-EV-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }} - Approval Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: white;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
        .cause-purple { color: #6d28d9; }
        .bg-cause-purple { background-color: #6d28d9; }
        .border-cause-purple { border-color: #6d28d9; }
    </style>
</head>
<body class="bg-gray-100 p-0 md:p-8">
    
    <div class="max-w-4xl mx-auto no-print mb-4 flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
        <p class="text-sm text-gray-600 font-bold">PDF PREVIEW MODE</p>
        <button onclick="window.print()" class="bg-cause-purple text-white px-6 py-2 rounded-lg font-bold shadow-lg hover:scale-105 transition-all">
            SAVE AS PDF / PRINT
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-sm border border-gray-200 overflow-hidden print-container" id="approval-form-document">
        <!-- Header -->
        <div class="p-8 border-b-4 border-cause-purple bg-gray-50 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <img src="https://admission.cust.edu.pk/web/image/website/1/logo?unique=f3e0a29" alt="CUST Logo" class="h-20 w-auto object-contain">
                <div class="border-l-2 border-gray-300 h-14"></div>
                <img src="{{ asset('images/cause-logo.png') }}" alt="CAUSE Logo" class="h-20 w-auto object-contain">
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">Event Approval Form</h2>
                <p class="text-[10px] font-bold text-cause-purple tracking-widest mt-1">CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY</p>
                <div class="mt-2">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Document ID</p>
                    <p class="text-sm font-mono font-bold text-gray-800">CAUSE-EV-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-10 space-y-10">
            <!-- Event Overview -->
            <section>
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4 border-b border-gray-100 pb-2">01. Event Information</h3>
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Event Title</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $event->title }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Academic Term</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $event->term->term_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Execution Date</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $event->expected_date->format('D, M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Proposed Venue</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $event->venue }}</p>
                    </div>
                </div>
            </section>

            <!-- Financial Summary -->
            <section>
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4 border-b border-gray-100 pb-2">02. Financial Allocation</h3>
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                <th class="pb-4">Allocated Item</th>
                                <th class="pb-4 text-center">Quantity</th>
                                <th class="pb-4 text-center">Unit Rate</th>
                                <th class="pb-4 text-right">Approved Amount</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($event->items->where('is_approved_by_hod', true) as $item)
                            <tr class="border-t border-gray-200/50">
                                <td class="py-4 font-medium text-gray-800">{{ $item->item_name }}</td>
                                <td class="py-4 text-center text-gray-600 font-bold">{{ $item->quantity }}</td>
                                <td class="py-4 text-center text-gray-600 font-medium">PKR {{ number_format($item->unit_rate, 2) }}</td>
                                <td class="py-4 text-right font-black text-cause-purple">PKR {{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200">
                                <td colspan="2" class="pt-6 text-right font-black text-gray-900 text-lg uppercase tracking-tight">Grand Total Authorized</td>
                                <td class="pt-6 text-right font-black text-cause-purple text-2xl">PKR {{ number_format($event->grand_total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <!-- Signatures -->
            <section class="pt-10">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-8 border-b border-gray-100 pb-2">03. Authorization & Validation</h3>
                <div class="flex justify-between items-end">
                    <!-- Student -->
                    <div class="text-center w-48">
                        <p class="text-sm font-bold text-gray-900 mb-1">{{ $event->student->name }}</p>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Event Organizer</p>
                    </div>

                    <!-- Patron -->
                    <div class="text-center w-48">
                        <p class="text-sm font-bold text-gray-800 mb-1 uppercase tracking-tighter">VERIFIED</p>
                        <div class="border-b-2 border-gray-300 mb-2"></div>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Patron Review</p>
                    </div>

                    <!-- HOD -->
                    <div class="text-center w-64 relative">
                        <div class="h-24 flex items-center justify-center mb-4 relative">
                            @php
                                $settings = $event->signature_settings ?? [];
                                $sigScale = ($settings['sig_scale'] ?? 100) / 100;
                                $sigY = $settings['sig_y'] ?? 0;
                                $stampScale = ($settings['stamp_scale'] ?? 100) / 100;
                                $stampRotate = $settings['stamp_rotate'] ?? 12;
                                $stampX = $settings['stamp_x'] ?? -40;
                                $stampY = $settings['stamp_y'] ?? -40;
                            @endphp

                            @if($hod && $hod->digital_signature)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($hod->digital_signature) }}" 
                                     alt="Signature" 
                                     class="max-h-full max-w-full object-contain"
                                     style="transform: scale({{ $sigScale }}) translateY({{ $sigY }}px);">
                            @else
                                <div class="text-xs text-gray-400 italic">Digitally Signed</div>
                            @endif
                            
                            @if($hod && $hod->digital_stamp)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($hod->digital_stamp) }}" 
                                     alt="Stamp" 
                                     class="absolute w-24 h-24 object-contain opacity-70"
                                     style="top: -2.5rem; right: -2.5rem; transform: scale({{ $stampScale }}) translate({{ $stampX }}px, {{ $stampY }}px) rotate({{ $stampRotate }}deg);">
                            @endif
                        </div>
                        <div class="border-b-2 border-cause-purple mb-2"></div>
                        <p class="text-sm font-bold text-gray-900 mb-1">{{ $hod ? $hod->name : 'N/A' }}</p>
                        <p class="text-[10px] font-black text-cause-purple uppercase tracking-widest">Head of Department (HOD)</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <div class="bg-gray-900 p-6 text-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em]">This document is digitally verified by CAUSE Smart Society System</p>
        </div>
    </div>

    <script>
        // Auto-trigger print dialog
        window.onload = function() {
            // Check if we should auto-print (optional)
            // window.print();
        };
    </script>
</body>
</html>
