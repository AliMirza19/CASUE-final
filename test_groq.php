<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$key = env('GROQ_API_KEY');
$url = 'https://api.groq.com/openai/v1/chat/completions';

$payload = json_encode([
    'model' => 'llama-3.3-70b-versatile',
    'messages' => [
        ['role' => 'user', 'content' => 'Say exactly: CAUSE-AI (Groq) is online!']
    ],
    'max_tokens' => 20
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $key
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$res = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo "CURL ERROR: " . $err . "\n";
} else {
    echo "HTTP Status: " . $httpCode . "\n";
    $data = json_decode($res, true);
    if (isset($data['choices'][0]['message']['content'])) {
        echo "✅ SUCCESS: " . $data['choices'][0]['message']['content'] . "\n";
    } else {
        echo "❌ API ERROR:\n" . $res . "\n";
    }
}
