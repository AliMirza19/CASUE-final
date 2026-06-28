import os

sidebar_dir = r"c:\xampp\htdocs\cause-society\resources\views\partials"

# HOD Sidebar
hod_sidebar = os.path.join(sidebar_dir, "hod-sidebar.blade.php")
if os.path.exists(hod_sidebar):
    with open(hod_sidebar, "r", encoding="utf-8") as f:
        content = f.read()
    
    # Remove "Select President" if it exists
    content = content.replace(""">Select President</a>""", """>Review Election Candidates</a>""")
    # Ensure it points to selection.hod
    if 'selection.hod' not in content:
        link = """<a href="{{ route('selection.hod') }}" class="sidebar-link {{ request()->routeIs('selection.hod') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
    <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
    Review Election Candidates
</a>"""
        content = content.replace('Dashboard</a>', 'Dashboard</a>\n' + link)
    else:
        # Update existing selection.hod link text
        content = content.replace('Select President', 'Review Election Candidates')

    with open(hod_sidebar, "w", encoding="utf-8") as f:
        f.write(content)

# Patron Sidebar
patron_sidebar = os.path.join(sidebar_dir, "patron-sidebar.blade.php")
if os.path.exists(patron_sidebar):
    with open(patron_sidebar, "r", encoding="utf-8") as f:
        content = f.read()
    
    # Point existing patron.candidates to selection.patron
    content = content.replace("route('patron.candidates')", "route('selection.patron')")
    content = content.replace("request()->routeIs('patron.candidates')", "request()->routeIs('selection.patron')")
    
    # Remove my newly added "Shortlist President" link to avoid duplicates
    if 'Shortlist President' in content:
        import re
        content = re.sub(r'<a href="{{ route\(\'selection\.patron\'\)"[^>]*>.*?Shortlist President.*?</a>', '', content, flags=re.DOTALL)

    with open(patron_sidebar, "w", encoding="utf-8") as f:
        f.write(content)

print("Sidebars updated and merged into 'Review Election Candidates'.")
