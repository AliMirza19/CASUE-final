import os

sidebar_dir = r"c:\xampp\htdocs\cause-society\resources\views\partials"

committee_chat_logic = """
@php
    $isCommitteeMember = \\App\\Models\\CommitteeMember::where('faculty_user_id', auth()->id())
        ->whereHas('committee', function($q) { $q->where('is_active', true); })->exists();
@endphp
@if($isCommitteeMember)
<a href="{{ route('selection.discussion') }}" class="sidebar-link {{ request()->routeIs('selection.discussion') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
    Committee Chat
</a>
@endif
"""

# HOD Sidebar
hod_sidebar = os.path.join(sidebar_dir, "hod-sidebar.blade.php")
if os.path.exists(hod_sidebar):
    with open(hod_sidebar, "r", encoding="utf-8") as f:
        content = f.read()
    if 'selection.hod' not in content:
        link = """<a href="{{ route('selection.hod') }}" class="sidebar-link {{ request()->routeIs('selection.hod') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
    <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
    Select President
</a>"""
        content = content.replace('Dashboard</a>', 'Dashboard</a>\n' + link)
    if 'selection.discussion' not in content:
        content = content.replace('Direct Messages</a>', 'Direct Messages</a>\n' + committee_chat_logic)
    with open(hod_sidebar, "w", encoding="utf-8") as f:
        f.write(content)

# Patron Sidebar
patron_sidebar = os.path.join(sidebar_dir, "patron-sidebar.blade.php")
if os.path.exists(patron_sidebar):
    with open(patron_sidebar, "r", encoding="utf-8") as f:
        content = f.read()
    if 'selection.patron' not in content:
        link = """<a href="{{ route('selection.patron') }}" class="sidebar-link {{ request()->routeIs('selection.patron') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
    <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    Shortlist President
</a>"""
        content = content.replace('Dashboard\n    </a>', 'Dashboard\n    </a>\n' + link)
    if 'selection.discussion' not in content:
        content = content.replace('Direct Messages\n', 'Direct Messages\n' + committee_chat_logic)
    with open(patron_sidebar, "w", encoding="utf-8") as f:
        f.write(content)

# Faculty & Team Sidebars (Global Committee Chat only)
others = ["faculty-sidebar.blade.php", "admin-sidebar.blade.php", "team-sidebar.blade.php"]
for other in others:
    file_path = os.path.join(sidebar_dir, other)
    if os.path.exists(file_path):
        with open(file_path, "r", encoding="utf-8") as f:
            content = f.read()
        if 'selection.discussion' not in content:
            if 'Direct Messages' in content:
                content = content.replace('Direct Messages', 'Direct Messages' + committee_chat_logic)
            else:
                content += committee_chat_logic
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(content)

print("Sidebars updated successfully.")
