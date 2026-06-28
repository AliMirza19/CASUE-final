import os

path = 'resources/views/dashboards'
for filename in os.listdir(path):
    if filename.endswith('.blade.php'):
        filepath = os.path.join(path, filename)
        with open(filepath, 'r') as f:
            content = f.read()
        
        new_content = content.replace(
            "<!-- Main body empty for announcements (handled by layout) -->",
            "@include('partials.announcements-feed')"
        )
        
        with open(filepath, 'w') as f:
            f.write(new_content)
print("Updated all dashboards.")
