<?php

$dir = __DIR__ . '/resources/views/faculty';
$files = glob($dir . '/*.blade.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace the hardcoded sidebar section with the include
    $newContent = preg_replace(
        '/@section\(\'sidebar\'\)[\s\S]*?@endsection/s',
        "@section('sidebar')\n    @include('partials.faculty-sidebar')\n@endsection",
        $content
    );
    
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "Updated " . basename($file) . "\n";
    }
}
echo "Done.\n";
