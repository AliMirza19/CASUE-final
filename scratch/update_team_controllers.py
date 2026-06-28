import os
import re

controllers_dir = r"c:\xampp\htdocs\cause-society\app\Http\Controllers"
roles = ["Video", "Smt", "Photo", "Doc", "Deco"]

for role in roles:
    file_path = os.path.join(controllers_dir, role, "DashboardController.php")
    if os.path.exists(file_path):
        with open(file_path, "r", encoding="utf-8") as f:
            content = f.read()
        
        # Update index() to fetch announcements
        if 'public function index()' in content:
            replacement = """    public function index()
    {
        $announcements = \\\\App\\\\Models\\\\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.""" + role.lower() + """', compact('announcements'));
    }"""
            # Using a simpler string replacement for index() to avoid re.sub issues
            index_pattern = re.compile(r'public function index\(\)\s*\{[^}]*\}', re.DOTALL)
            content = index_pattern.sub(replacement, content)
        
        # Replace globalAnnouncements with announcements in other methods if they exist
        content = content.replace('$globalAnnouncements', '$announcements')
        content = content.replace("'globalAnnouncements'", "'announcements'")
        
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(content)
        print(f"Updated {role} DashboardController")
