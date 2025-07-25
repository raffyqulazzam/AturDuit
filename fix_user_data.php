<?php

// Fix existing categories by assigning them to the first user
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the first user
    $firstUser = App\Models\User::first();
    
    if ($firstUser) {
        // Update all categories with user_id = 0 to belong to first user
        $updated = App\Models\Category::where('user_id', 0)->update(['user_id' => $firstUser->id]);
        echo "Updated {$updated} categories to belong to user {$firstUser->id} ({$firstUser->name})\n";
        
        // Also check accounts
        $updatedAccounts = App\Models\Account::where('user_id', 0)->update(['user_id' => $firstUser->id]);
        echo "Updated {$updatedAccounts} accounts to belong to user {$firstUser->id}\n";
        
        echo "Fix completed successfully!\n";
    } else {
        echo "No users found in database.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
