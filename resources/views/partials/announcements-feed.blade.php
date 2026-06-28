<!-- Recent Announcements Section -->
<div class="mt-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <span class="bg-indigo-100 p-2 rounded-lg mr-3">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
            </span>
            Recent Announcements
        </h2>
        @if(auth()->user()->isAppointedHod() || auth()->user()->role === 'president' || auth()->user()->isAppointedPatron() || auth()->user()->role === 'admin')
            <a href="{{ route('announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg hover:shadow-indigo-200 group">
                <svg class="w-5 h-5 mr-2 transform group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Post New
            </a>
        @endif
    </div>

    @if($announcements->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100">
            <img src="https://cdn-icons-png.flaticon.com/512/5089/5089765.png" alt="No announcements" class="w-24 h-24 mx-auto opacity-20 mb-4">
            <p class="text-gray-500 font-medium italic">No recent announcements to show.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($announcements as $announcement)
                <div class="group relative bg-white border border-gray-100 shadow-indigo-100/50 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <!-- Image Wrapper -->
                    <div class="relative h-48 overflow-hidden cursor-pointer" onclick="document.getElementById('announcement-modal-{{ $announcement->id }}').classList.remove('hidden')">
                        <img src="{{ $announcement->image_url ?? 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&q=80&w=1000' }}" 
                             alt="{{ $announcement->title }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        
                        <!-- Top Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <!-- Ownership Badge -->
                            @if($announcement->user_id === auth()->id())
                                <div class="bg-yellow-400 text-yellow-900 text-[9px] font-black uppercase px-3 py-1 rounded-full shadow-lg flex items-center border border-yellow-300">
                                    <span class="mr-1">🌟</span> Your Post
                                </div>
                            @endif
                        </div>

                        <!-- Role Badge (Bottom Left) -->
                        <div class="absolute bottom-4 left-4">
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold uppercase rounded-full border border-white/30">
                                {{ ucfirst($announcement->creator->role ?? 'Staff') }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                            {{ $announcement->title }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-6 line-clamp-3 leading-relaxed">
                            {{ $announcement->description }}
                        </p>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                            <!-- Author Info -->
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold mr-3 shadow-md">
                                    {{ substr($announcement->creator->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="text-[10px]">
                                    <p class="font-bold text-gray-800 leading-none mb-1">{{ $announcement->creator->name ?? 'Unknown' }}</p>
                                    <p class="text-gray-400 font-medium leading-none">{{ $announcement->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <button onclick="document.getElementById('announcement-modal-{{ $announcement->id }}').classList.remove('hidden')" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all">
                                    View Details
                                </button>
                                @if($announcement->user_id === auth()->id())
                                    <a href="{{ route('announcements.edit', $announcement->id) }}" class="p-2 bg-gray-50 hover:bg-yellow-50 text-gray-400 hover:text-yellow-600 rounded-xl transition-all border border-transparent hover:border-yellow-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600 rounded-xl transition-all border border-transparent hover:border-red-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @elseif($announcement->link_url)
                                    <a href="{{ $announcement->link_url }}" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-indigo-100 hover:shadow-indigo-200">
                                        Open Link
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcement Detail Modal -->
                <div id="announcement-modal-{{ $announcement->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('announcement-modal-{{ $announcement->id }}').classList.add('hidden')"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-white/20">
                            <div class="relative">
                                <!-- Modal Image -->
                                <div class="h-80 w-full overflow-hidden">
                                    <img src="{{ $announcement->image_url ?? 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&q=80&w=1000' }}" 
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent"></div>
                                </div>
                                
                                <!-- Close Button -->
                                <button onclick="document.getElementById('announcement-modal-{{ $announcement->id }}').classList.add('hidden')" class="absolute top-4 right-4 bg-black/20 hover:bg-black/40 backdrop-blur-md text-white p-2 rounded-full transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="px-8 pb-8 -mt-12 relative z-10">
                                <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-50">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-[10px] font-bold uppercase rounded-full">
                                            {{ ucfirst($announcement->creator->role ?? 'Staff') }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-medium">{{ $announcement->created_at->format('F d, Y') }}</span>
                                    </div>
                                    <h3 class="text-3xl font-black text-gray-800 mb-4 leading-tight">{{ $announcement->title }}</h3>
                                    <div class="flex items-center mb-6">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-lg font-bold mr-4 shadow-lg">
                                            {{ substr($announcement->creator->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-800 leading-none mb-1 text-lg">{{ $announcement->creator->name ?? 'Unknown' }}</p>
                                            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Posted by</p>
                                        </div>
                                    </div>
                                    <div class="prose prose-indigo max-w-none">
                                        <p class="text-gray-600 text-lg leading-relaxed whitespace-pre-line">
                                            {{ $announcement->description }}
                                        </p>
                                    </div>
                                    
                                    @if($announcement->link_url)
                                        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                                            <a href="{{ $announcement->link_url }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-indigo-100">
                                                Visit External Link
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
