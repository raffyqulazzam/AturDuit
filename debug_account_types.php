<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing AccountType queries...\n";

try {
    // Get first user
    $user = App\Models\User::first();
    echo "User ID: " . $user->id . "\n";
    
    // Test the controller logic
    $accountTypes = App\Models\AccountType::where('user_id', $user->id)
        ->orderBy('name')
        ->get();
    
    echo "Found " . $accountTypes->count() . " account types\n";
    
    foreach ($accountTypes as $accountType) {
        $accountType->user_accounts_count = $accountType->accounts()
            ->where('user_id', $user->id)
            ->count();
            
        $accountType->load(['accounts' => function($query) use ($user) {
            $query->select('id', 'account_type_id', 'name', 'balance', 'currency', 'is_active')
                  ->where('user_id', $user->id)
                  ->where('is_active', true)
                  ->take(3);
        }]);
        
        // Set userAccounts as alias for accounts for blade compatibility
        $accountType->userAccounts = $accountType->accounts;
        
        echo "AccountType {$accountType->name}: {$accountType->user_accounts_count} accounts, loaded: " . $accountType->userAccounts->count() . "\n";
    }
    
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
