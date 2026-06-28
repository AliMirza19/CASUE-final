<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Look for either STU-001 or role = sa
$user = App\Models\User::where('reg_id', 'STU-001')->orWhere('role', 'sa')->first();
if ($user) {
    $user->password = Illuminate\Support\Facades\Hash::make('Alimirza1@');
    $user->save();
    echo "SA/STU-001 password updated successfully. Reg ID: " . $user->reg_id . ", Role: " . $user->role . "\n";
} else {
    echo "SA/STU-001 user not found.\n";
}
