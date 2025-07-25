<?php

// Quick test script to check database structure
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test Category model
    $categories = App\Models\Category::all();
    echo "Categories table exists and has " . $categories->count() . " records.\n";
    
    if ($categories->count() > 0) {
        $firstCategory = $categories->first();
        echo "First category structure:\n";
        echo "- ID: " . ($firstCategory->id ?? 'null') . "\n";
        echo "- Name: " . ($firstCategory->name ?? 'null') . "\n";
        echo "- User ID: " . ($firstCategory->user_id ?? 'null') . "\n";
        echo "- Type: " . ($firstCategory->type ?? 'null') . "\n";
    }
    
    // Test User model
    $users = App\Models\User::all();
    echo "\nUsers table has " . $users->count() . " records.\n";
    
    echo "\nDatabase connection is working!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
