<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAUSE-EV-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }} - Approval Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: white;
            color: black;
            margin: 0;
            padding: 0;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 2px solid black; padding: 4px 8px; vertical-align: top; }
        .section-title {
            background-color: black;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            padding: 4px;
        }
        .header-cell {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        .value-cell {
            font-size: 14px;
            min-height: 20px;
        }
        .thick-border { border: 3px solid black; }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto no-print mb-4 flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
        <p class="text-sm text-gray-600 font-bold">PDF PREVIEW MODE</p>
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold shadow-lg hover:scale-105 transition-all">
            SAVE AS PDF / PRINT
        </button>
    </div>

    <div class="form-container">
        <!-- Top Header -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <div style="width: 25%; border: 3px solid black; padding: 10px; display: flex; align-items: center; justify-content: center;">
                <img src="https://admission.cust.edu.pk/web/image/website/1/logo?unique=f3e0a29" alt="CUST Logo" style="max-height: 100px;">
            </div>
            <div style="width: 73%; border: 3px solid black; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 10px;">
                <h1 style="text-decoration: underline; font-size: 20px; margin: 0 0 10px 0;">AUTHORIZATION FORM</h1>
                <p style="margin: 0; font-size: 14px; font-weight: bold;">(SEMINAR / EDUCATIONAL OR INDUSTRY VISIT / RECREATIONAL VISIT / EXHIBITION / WALK / WORKSHOP / TRAINING)</p>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="section-title">BASIC INFORMATION</div>
        <table>
            <tr>
                <td style="width: 50%;">
                    <div class="header-cell">TITLE OF THE ACTIVITY</div>
                    <div class="value-cell">{{ $event->title }}</div>
                </td>
                <td style="width: 50%;">
                    <div class="header-cell">DATE AND TIME</div>
                    <div class="value-cell">{{ $event->expected_date->format('M d, Y') }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="header-cell">PROPOSED VENUE</div>
                    <div class="value-cell">{{ $event->venue ?? '-' }}</div>
                </td>
                <td>
                    <div class="header-cell">DURATION</div>
                    <div class="value-cell">-</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="header-cell">SOCIETY / PATRON</div>
                    <div class="value-cell">Capital University Software Engineering Society (CAUSE)</div>
                </td>
                <td>
                    <div class="header-cell">GUEST SPEAKER NAME & DESIGNATION</div>
                    <div class="value-cell">{{ $event->guest_speaker_name ? $event->guest_speaker_name . ($event->guest_speaker_designation ? ' - ' . $event->guest_speaker_designation : '') : '-' }}</div>
                </td>
            </tr>
        </table>

        <!-- Students' Details -->
        <div class="section-title" style="margin-top: 5px;">STUDENTS' DETAILS</div>
        <table style="text-align: center;">
            <tr class="header-cell">
                <td style="width: 40%;">NAME</td>
                <td style="width: 30%;">REG. NO.</td>
                <td style="width: 30%;">ROLE</td>
            </tr>
            <!-- We can put the event creator as the primary contact, and any volunteers if they exist -->
            <tr>
                <td class="value-cell">{{ $event->student->name }}</td>
                <td class="value-cell">{{ $event->student->reg_id ?? '-' }}</td>
                <td class="value-cell">{{ ucfirst($event->student->role) }}</td>
            </tr>
            <tr><td class="value-cell">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td class="value-cell">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td class="value-cell">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td class="value-cell">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        </table>
        <table>
            <tr>
                <td style="width: 50%;">
                    <span class="header-cell">Contact Person's Name:</span> <span class="value-cell">{{ $event->student->name }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="header-cell">Reg. No.:</span> <span class="value-cell">{{ $event->student->reg_id ?? '-' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="header-cell">Mobile:</span> <span class="value-cell">{{ $event->student->profile->phone_number ?? '-' }}</span>
                </td>
            </tr>
        </table>

        <!-- Expected Budget -->
        <div class="section-title" style="margin-top: 5px;">EXPECTED BUDGET</div>
        <table style="text-align: center;">
            <tr class="header-cell">
                <td style="width: 10%;">S. No.</td>
                <td style="width: 50%;">Requirements</td>
                <td style="width: 20%;">Rate</td>
                <td style="width: 20%;">Amount</td>
            </tr>
            @php $count = 1; @endphp
            @foreach($event->items->where('is_approved_by_hod', true) as $item)
            <tr>
                <td class="header-cell">{{ $count++ }}</td>
                <td class="value-cell" style="text-align: left;">{{ $item->item_name }}</td>
                <td class="value-cell">{{ number_format($item->unit_rate, 2) }}</td>
                <td class="value-cell">{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
            <!-- Fill remaining empty rows to match 9 rows from image -->
            @for($i = $count; $i <= 9; $i++)
            <tr>
                <td class="header-cell">{{ $i }}</td>
                <td class="value-cell">&nbsp;</td>
                <td class="value-cell">&nbsp;</td>
                <td class="value-cell">&nbsp;</td>
            </tr>
            @endfor
            <tr>
                <td colspan="3" class="header-cell">TOTAL</td>
                <td class="value-cell font-bold">{{ number_format($event->grand_total, 2) }}</td>
            </tr>
        </table>

        <!-- Signatures -->
        <div class="section-title" style="margin-top: 5px;">SIGNATURES WITH COMMENTS IF ANY ARE NECESSARY</div>
        <table style="table-layout: fixed;">
            <tr>
                <td style="height: 100px; position: relative;">
                    <div class="header-cell">Society Patron</div>
                    @php
                        // Find patron signature if available
                        $patronAssignment = \App\Models\RoleAssignment::where('role', 'patron')->where('term_id', $event->term_id)->first();
                        $patron = $patronAssignment ? $patronAssignment->user : \App\Models\User::where('role', 'patron')->whereNotNull('digital_signature')->first(); // Fallback
                    @endphp
                    @if($patron && $patron->digital_signature)
                        <div style="position: absolute; top: 30px; left: 0; right: 0; text-align: center;">
                            <img src="{{ asset('storage/' . $patron->digital_signature) }}" style="max-height: 50px; max-width: 100%; object-fit: contain;">
                        </div>
                    @endif
                </td>
                <td style="height: 100px; position: relative;">
                    <div class="header-cell">HoD</div>
                    @if($hod && $hod->digital_signature)
                        <div style="position: absolute; top: 30px; left: 0; right: 0; text-align: center;">
                            <img src="{{ asset('storage/' . $hod->digital_signature) }}" style="max-height: 50px; max-width: 100%; object-fit: contain;">
                        </div>
                    @endif
                </td>
                <td style="height: 100px;">
                    <div class="header-cell">DSA</div>
                </td>
                <td style="height: 100px;">
                    <div class="header-cell">Vice Chancellor</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 10px;"><div class="header-cell">DATE <span style="font-weight: normal; margin-left: 10px;">{{ $event->updated_at->format('d/m/Y') }}</span></div></td>
                <td style="padding-top: 10px;"><div class="header-cell">DATE <span style="font-weight: normal; margin-left: 10px;">{{ $event->updated_at->format('d/m/Y') }}</span></div></td>
                <td style="padding-top: 10px;"><div class="header-cell">DATE</div></td>
                <td style="padding-top: 10px;"><div class="header-cell">DATE</div></td>
            </tr>
        </table>

        <!-- Footer -->
        <div style="border: 3px solid black; margin-top: 5px; text-align: center; padding: 5px;">
            <div style="font-size: 11px; font-weight: bold;">THIS FORM SHOULD BE SUBMITTED WITH THE STUDENT AFFAIRS OFFICE BY SOCIETY PATRON, PREFERABLY</div>
            <div style="font-size: 16px; font-weight: bold;">ONE WEEK BEFORE THE EVENT <span style="font-size: 11px;">FOR TIMELY APPROVAL PROCEEDINGS.</span></div>
        </div>

    </div>

</body>
</html>
