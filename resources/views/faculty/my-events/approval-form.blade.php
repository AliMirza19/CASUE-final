@extends('layouts.dashboard')

@section('title', 'Event Approval Form - CAUSE Smart Society')

@section('sidebar')
    @include('partials.student-sidebar')
@endsection

@section('content')
    <div class="mb-6 no-print">
        <div class="flex justify-between items-center">
            <a href="{{ route('student.events.show', $event->id) }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Details
            </a>
            <button onclick="window.print()" class="bg-cause-purple text-white px-6 py-2 rounded-lg font-bold flex items-center shadow-lg hover:bg-cause-purple-dark transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Download / Print Form
            </button>
        </div>
    </div>

    <div class="max-w-4xl mx-auto print:m-0 print:p-0">
        <!-- Formal Approval Document -->
        <div class="bg-white shadow-2xl rounded-sm border border-gray-200 overflow-hidden mb-8 print:shadow-none print:border-none" id="approval-form-document">
            <!-- Header -->
            <div class="p-8 border-b-4 border-cause-purple bg-gray-50 flex justify-between items-center print:bg-white">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">Event Approval Form</h2>
                    <p class="text-sm font-bold text-cause-purple tracking-widest mt-1">CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY (CAUSE)</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Document ID</p>
                    <p class="text-sm font-mono font-bold text-gray-800">CAUSE-EV-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}</p>
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
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 print:bg-white">
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
                            <p class="text-sm font-script text-gray-400 italic mb-1">{{ $event->student->name }}</p>
                            <div class="border-b-2 border-gray-300 mb-2"></div>
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Event Organizer</p>
                        </div>

                        <!-- Patron -->
                        <div class="text-center w-48">
                            <p class="text-sm font-bold text-gray-800 mb-1 uppercase tracking-tighter">Approved via System</p>
                            <div class="border-b-2 border-gray-300 mb-2"></div>
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Patron Review</p>
                        </div>

                        <!-- HOD -->
                        <div class="text-center w-64 relative">
                            <div class="h-24 flex items-center justify-center mb-4 relative">
                                @if($hod && $hod->digital_signature)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($hod->digital_signature) }}" alt="Signature" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="text-xs text-gray-400 italic">Digitally Signed</div>
                                @endif
                                
                                @if($hod && $hod->digital_stamp)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($hod->digital_stamp) }}" alt="Stamp" class="absolute -top-10 -right-10 w-24 h-24 object-contain opacity-70 transform rotate-12">
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
            <div class="bg-gray-900 p-6 text-center print:bg-gray-100">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.3em] print:text-gray-900">This document is digitally verified by CAUSE Smart Society System</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background-color: white !important;
        }
        .sidebar {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        header {
            display: none !important;
        }
    }
</style>
@endpush
