@extends('layouts.dashboard')

@section('title', 'HOD Settings - CAUSE Smart Society')
@section('page-title', 'HOD Settings')
@section('page-description', 'Manage your digital signature and department stamp')

@section('sidebar')
    @include('partials.hod-sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('hod.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-800">Digital Authorization Assets</h3>
                <p class="text-sm text-gray-500 mt-1">These assets are used to formalize event approvals and budget allocations.</p>
            </div>

            <div class="p-8">
                <form action="{{ route('hod.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- Digital Signature -->
                        <div class="space-y-4">
                            <label class="block text-sm font-black text-gray-700 uppercase tracking-widest">Digital Signature</label>
                            <div class="relative group">
                                <div class="w-full h-48 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center bg-gray-50 group-hover:border-cause-purple transition-all duration-300 cursor-pointer overflow-hidden relative">
                                    <input type="file" name="digital_signature" id="sig-input" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*">
                                    
                                    @if($user->digital_signature)
                                        <img src="{{ asset('storage/' . $user->digital_signature) }}" class="max-h-full object-contain p-6" id="sig-display">
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white text-xs font-bold uppercase tracking-widest">Change Signature</span>
                                        </div>
                                    @else
                                        <div class="text-center p-6" id="sig-placeholder">
                                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-gray-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </div>
                                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Upload Signature</p>
                                        </div>
                                        <img src="" class="max-h-full object-contain p-6 hidden" id="sig-display">
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-start space-x-2 text-xs text-gray-500 italic">
                                <svg class="w-4 h-4 text-cause-purple mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <span>For best results, use a transparent PNG of your signature on white paper.</span>
                            </div>
                        </div>

                        <!-- Digital Stamp Removed as per request -->

                    <div class="pt-6 border-t border-gray-50">
                        <button type="submit" class="w-full bg-gradient-to-r from-cause-purple to-cause-purple-dark text-white font-black py-4 px-8 rounded-2xl shadow-lg hover:shadow-2xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 tracking-widest uppercase text-sm">
                            Save Digital Assets
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Identity Notice -->
        <div class="mt-8 bg-gray-900 rounded-2xl p-6 text-white relative overflow-hidden">
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white/5 transform rotate-12" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z" />
            </svg>
            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-cause-purple mb-2">Institutional Verification</h4>
            <p class="text-sm text-gray-400 leading-relaxed">
                As the Head of Department, your digital authorization carries weight in the society's financial and operational decisions. Please ensure your signature and stamp are authentic and represent the department's formal approval.
            </p>
        </div>
    </div>

    <script>
        // Preview logic
        document.getElementById('sig-input').onchange = evt => {
            const [file] = evt.target.files;
            if (file) {
                const display = document.getElementById('sig-display');
                const placeholder = document.getElementById('sig-placeholder');
                display.src = URL.createObjectURL(file);
                display.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
        }
    </script>
@endsection
