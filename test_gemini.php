<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$key = env('GEMINI_API_KEY');

// Test multiple model + endpoint combinations
$tests = [
    ['v1',    'gemini-1.5-flash'],
    ['v1',    'gemini-1.5-flash-latest'],
    ['v1beta','gemini-1.5-flash'],
    ['v1beta','gemini-2.0-flash'],
    ['v1beta','gemini-2.0-flash-lite'],
    ['v1beta','gemini-1.5-flash-latest'],
];

$payload = json_encode([
    'contents' => [['role'=>'user','parts'=>[['text'=>'Say: OK']]]],
    'generationConfig' => ['maxOutputTokens' => 10]
]);

foreach ($tests as [$version, $model]) {
    $url = "https://generativelanguage.googleapis.com/{$version}/models/{$model}:generateContent?key={$key}";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo "[{$version}/{$model}] CURL ERR: {$err}\n";
        continue;
    }
    $data = json_decode($res, true);
    if ($code === 200 && isset($data['candidates'])) {
        echo "✅ WORKS: {$version}/{$model}\n";
        echo "   Response: " . $data['candidates'][0]['content']['parts'][0]['text'] . "\n";
    } else {
        $msg = $data['error']['message'] ?? $res;
        echo "❌ [{$code}] {$version}/{$model} — {$msg}\n";
    }
}
