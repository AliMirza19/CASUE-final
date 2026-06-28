<?php

$dir = __DIR__ . '/app/Http/Controllers';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($file->getPathname());
        
        $modified = false;
        
        // Find public function profile()
        if (preg_match('/public function profile\(\)([\s:]*\w*)?\s*\{/s', $content)) {
            // Replace the return view(...) line inside the profile method
            $newContent = preg_replace(
                '/public function profile\(\)([\s:]*\w*)?\s*\{[\s\S]*?return view\([\'"][^\'"]+[\'"],\s*compact\([\'"]user[\'"]\)\);/s',
                "public function profile()$1\n    {\n        \$user = \Illuminate\Support\Facades\Auth::user();\n        return view('profile.show', compact('user'));",
                $content
            );
            
            if ($newContent !== $content) {
                $content = $newContent;
                $modified = true;
            }
        }
        
        if ($modified) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated {$file->getFilename()}\n";
        }
    }
}
echo "Done.\n";
