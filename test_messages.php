<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;
use App\Models\User;

echo "=== Testing Message Model ===\n\n";

// Check if messages table exists
try {
    $messagesCount = Message::count();
    echo "✓ Messages table accessible\n";
    echo "  Total messages in database: $messagesCount\n\n";
} catch (\Exception $e) {
    echo "✗ Error accessing messages table: " . $e->getMessage() . "\n";
    exit(1);
}

// Get the latest message
try {
    $latestMessage = Message::with(['sender', 'receiver'])->latest()->first();
    if ($latestMessage) {
        echo "✓ Latest message found:\n";
        echo "  ID: {$latestMessage->id}\n";
        echo "  From: {$latestMessage->sender->name} (ID: {$latestMessage->sender_id})\n";
        echo "  To: {$latestMessage->receiver->name} (ID: {$latestMessage->receiver_id})\n";
        echo "  Text: " . substr($latestMessage->message_text, 0, 50) . "...\n";
        echo "  Read: " . ($latestMessage->is_read ? 'Yes' : 'No') . "\n";
        echo "  Time: {$latestMessage->created_at}\n\n";
    } else {
        echo "ℹ No messages in database yet\n\n";
    }
} catch (\Exception $e) {
    echo "✗ Error fetching latest message: " . $e->getMessage() . "\n\n";
}

// Try to create a test message (find two users first)
try {
    $users = User::limit(2)->get();
    
    if ($users->count() >= 2) {
        $sender = $users[0];
        $receiver = $users[1];
        
        echo "✓ Found test users:\n";
        echo "  Sender: {$sender->name} (ID: {$sender->id})\n";
        echo "  Receiver: {$receiver->name} (ID: {$receiver->id})\n\n";
        
        echo "Attempting to create test message...\n";
        
        $testMessage = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message_text' => 'Test message created at ' . now(),
            'is_read' => false
        ]);
        
        echo "✓ Test message created successfully!\n";
        echo "  Message ID: {$testMessage->id}\n";
        echo "  Text: {$testMessage->message_text}\n\n";
        
        // Clean up - delete the test message
        $testMessage->delete();
        echo "✓ Test message deleted (cleanup)\n\n";
        
    } else {
        echo "⚠ Not enough users in database to test message creation\n\n";
    }
} catch (\Exception $e) {
    echo "✗ Error creating test message: " . $e->getMessage() . "\n";
    echo "  Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

echo "=== Test Complete ===\n";
