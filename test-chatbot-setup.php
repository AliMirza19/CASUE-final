<?php
/**
 * CAUSE-AI Chatbot Setup Verification Script
 * Run this after setting up your database and Gemini API key
 */

echo "=== CAUSE-AI Chatbot Setup Verification ===\n\n";

// Check 1: Migration file exists
$migrationFile = 'database/migrations/2026_04_15_000001_create_chat_histories_table.php';
echo "✓ Migration file: " . (file_exists($migrationFile) ? "EXISTS" : "MISSING") . "\n";

// Check 2: Model exists
$modelFile = 'app/Models/ChatHistory.php';
echo "✓ ChatHistory Model: " . (file_exists($modelFile) ? "EXISTS" : "MISSING") . "\n";

// Check 3: Controller exists
$controllerFile = 'app/Http/Controllers/ChatController.php';
echo "✓ ChatController: " . (file_exists($controllerFile) ? "EXISTS" : "MISSING") . "\n";

// Check 4: Frontend JS exists
$jsFile = 'resources/js/cause-ai-chatbot.js';
echo "✓ Chatbot Widget JS: " . (file_exists($jsFile) ? "EXISTS" : "MISSING") . "\n";

// Check 5: Routes configured
$routesFile = 'routes/web.php';
$routesContent = file_get_contents($routesFile);
$hasRoutes = strpos($routesContent, 'ai-chat') !== false;
echo "✓ API Routes: " . ($hasRoutes ? "CONFIGURED" : "MISSING") . "\n";

// Check 6: Layout updated
$layoutFile = 'resources/views/layouts/app.blade.php';
$layoutContent = file_get_contents($layoutFile);
$hasWidget = strpos($layoutContent, 'cause-ai-chatbot') !== false;
$hasCsrf = strpos($layoutContent, 'csrf-token') !== false;
echo "✓ Layout Integration: " . ($hasWidget ? "CONFIGURED" : "MISSING") . "\n";
echo "✓ CSRF Token Meta: " . ($hasCsrf ? "CONFIGURED" : "MISSING") . "\n";

// Check 7: Vite config
$viteFile = 'vite.config.js';
$viteContent = file_get_contents($viteFile);
$hasViteConfig = strpos($viteContent, 'cause-ai-chatbot.js') !== false;
echo "✓ Vite Config: " . ($hasViteConfig ? "CONFIGURED" : "MISSING") . "\n";

// Check 8: .env.example updated
$envExampleFile = '.env.example';
$envContent = file_get_contents($envExampleFile);
$hasGeminiKey = strpos($envContent, 'GEMINI_API_KEY') !== false;
echo "✓ .env.example: " . ($hasGeminiKey ? "UPDATED" : "MISSING GEMINI_API_KEY") . "\n";

echo "\n=== Next Steps ===\n";
echo "1. Add GEMINI_API_KEY to your .env file\n";
echo "2. Start your database server (MySQL)\n";
echo "3. Run: php artisan migrate\n";
echo "4. Run: npm install && npm run build\n";
echo "5. Test by logging in as HOD, Patron, or President\n";
echo "\nSetup guide: CAUSE_AI_CHATBOT_SETUP.md\n";
