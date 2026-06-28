@extends('layouts.dashboard')

@section('title', 'Final Approval Form - CAUSE Smart Society')
@section('page-title', 'Final Event Approval')
@section('page-description', 'Formalize event approval with digital signature and stamp')

@section('sidebar')
    @include('partials.hod-sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('hod.review', $event->id) }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Review
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('hod.approve', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="comments" value="{{ $comments }}">

            <!-- Formal Approval Document -->
            <div class="bg-white shadow-2xl rounded-sm border border-gray-200 overflow-hidden mb-8" id="approval-form-document">
                <!-- Header -->
                <div class="p-8 border-b-4 border-cause-purple bg-gray-50">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center space-x-4">
                            <img src="https://admission.cust.edu.pk/web/image/website/1/logo?unique=f3e0a29" alt="CUST Logo" class="h-16 w-auto object-contain">
                            <div class="h-12 w-px bg-gray-300 mx-2"></div>
                            <img src="{{ asset('images/cause-logo.png') }}" alt="CAUSE Logo" class="h-16 w-auto object-contain">
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Document ID</p>
                            <p class="text-sm font-mono font-bold text-gray-800">CAUSE-EV-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">Event Approval Form</h2>
                            <p class="text-sm font-bold text-cause-purple tracking-widest mt-1">CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY (CAUSE)</p>
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
                                <p class="text-lg font-bold text-gray-900 mt-1">{{ $budget->term->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase">Execution Date</p>
                                <p class="text-lg font-bold text-gray-900 mt-1">{{ \Carbon\Carbon::parse($event->expected_date)->format('D, M d, Y') }}</p>
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
                                <p class="text-sm font-script text-gray-400 italic mb-1">{{ $event->student->name }}</p>
                                <div class="border-b-2 border-gray-300 mb-2"></div>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Event Organizer</p>
                            </div>

                            <!-- Patron (Space reserved for flow, but label removed as requested) -->
                            <div class="text-center w-48 opacity-0 pointer-events-none">
                                <p class="text-sm font-bold text-gray-800 mb-1">APPROVED</p>
                                <div class="border-b-2 border-gray-300 mb-2"></div>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Patron Review</p>
                            </div>

                            <!-- HOD -->
                            <div class="text-center w-64 relative">
                                <div id="signature-preview-container" class="h-32 flex items-center justify-center mb-4 relative bg-gray-50/50 rounded-lg border-2 border-dashed border-gray-200">
                                    @if($user->digital_signature)
                                        <div id="sig-draggable" class="absolute cursor-move touch-none" style="transform: scale(1) translateY(0px);">
                                            <img src="{{ asset('storage/' . $user->digital_signature) }}" alt="Signature" class="max-h-24 object-contain pointer-events-none">
                                        </div>
                                    @else
                                        <div class="text-xs text-red-500 italic">Signature Required</div>
                                    @endif

                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest opacity-50">Drag & Resize Assets Above</span>
                                    </div>
                                </div>
                                <div class="border-b-2 border-cause-purple mb-2"></div>
                                <p class="text-[10px] font-black text-cause-purple uppercase tracking-widest">Head of Department (HOD)</p>
                                
                                <!-- Hidden inputs for positioning -->
                                <input type="hidden" name="sig_scale" id="sig_scale" value="100">
                                <input type="hidden" name="sig_y" id="sig_y" value="0">
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Footer -->
                <div class="bg-gray-900 p-6 text-center">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.3em]">This document is digitally verified by CAUSE Smart Society System</p>
                </div>
            </div>

            <!-- Instruction Banner -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mb-8 flex items-center space-x-4">
                <div class="bg-indigo-600 p-3 rounded-xl shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-indigo-900">Interactive Authorization</h4>
                    <p class="text-xs text-indigo-600 mt-1">You can now **drag** and **resize** your signature and stamp directly on the document below for perfect alignment.</p>
                </div>
            </div>

            <!-- Signature & Stamp Upload -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 mb-8">
                <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Update Digital Assets
                </h4>
                
                    <!-- Signature -->
                    <div class="space-y-4 md:col-span-2">
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest">Digital Signature</label>
                        <div class="relative group">
                            <div class="w-full h-32 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center bg-gray-50 group-hover:border-cause-purple transition-colors cursor-pointer overflow-hidden">
                                <input type="file" name="digital_signature" id="signature-input" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" {{ $user->digital_signature ? '' : 'required' }}>
                                @if($user->digital_signature)
                                    <img src="{{ asset('storage/' . $user->digital_signature) }}" class="max-h-full object-contain p-4" id="sig-prev">
                                @else
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="text-xs text-gray-400 font-bold">UPLOAD SIGNATURE</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-500">Recommended: Transparent PNG, 300x150px</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-cause-purple to-cause-purple-dark text-white font-black py-5 px-8 rounded-2xl shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 text-xl tracking-widest flex items-center justify-center">
                AUTHORIZE & FINAL APPROVE
                <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
        // State for transformations
        const state = {
            sig: { x: 0, y: 0, scale: 1, rotate: 0 }
        };

        function updateInputs() {
            document.getElementById('sig_scale').value = Math.round(state.sig.scale * 100);
            document.getElementById('sig_y').value = Math.round(state.sig.y);
        }

        function setupDraggable(id, key) {
            const element = document.getElementById(id);
            if (!element) return;

            interact(element)
                .draggable({
                    inertia: true,
                    listeners: {
                        move(event) {
                            state[key].x += event.dx;
                            state[key].y += event.dy;
                            event.target.style.transform = `translate(${state[key].x}px, ${state[key].y}px) scale(${state[key].scale}) rotate(${state[key].rotate}deg)`;
                            updateInputs();
                        }
                    }
                })
                .resizable({
                    edges: { left: true, right: true, bottom: true, top: true },
                    listeners: {
                        move(event) {
                            let { x, y, scale } = state[key];
                            
                            // Simple scaling based on width change
                            const ratio = event.rect.width / event.prevRect.width;
                            state[key].scale *= ratio;
                            
                            event.target.style.transform = `translate(${x}px, ${y}px) scale(${state[key].scale}) rotate(${state[key].rotate}deg)`;
                            updateInputs();
                        }
                    }
                });
            
            // Initial transform
            element.style.transform = `translate(${state[key].x}px, ${state[key].y}px) scale(${state[key].scale}) rotate(${state[key].rotate}deg)`;
        }

        setupDraggable('sig-draggable', 'sig');

        // File upload previews
        document.getElementById('signature-input').onchange = evt => {
            const [file] = evt.target.files;
            if (file) {
                const prev = document.getElementById('sig-prev');
                const mainPrev = document.querySelector('#sig-draggable img');
                const url = URL.createObjectURL(file);
                if (prev) prev.src = url;
                if (mainPrev) mainPrev.src = url;
            }
        }
    </script>
@endsection
