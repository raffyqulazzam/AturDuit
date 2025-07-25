<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database and run migrations with seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up AturDuit database...');

        // Get database configuration
        $database = config('database.connections.mysql.database');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $this->info("Creating database: {$database}");

        try {
            // Create database
            $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $pdo->exec($sql);
            
            $this->info("✓ Database '{$database}' created successfully!");

        } catch (PDOException $e) {
            $this->error("Failed to create database: " . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'Access denied')) {
                $this->warn('Please make sure MySQL is running and credentials are correct.');
                $this->warn('You can manually create the database using:');
                $this->line("CREATE DATABASE {$database} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            }
            
            return 1;
        }

        // Run migrations
        $this->info('Running migrations...');
        $this->call('migrate:fresh', ['--force' => true]);

        // Run seeders
        $this->info('Running seeders...');
        $this->call('db:seed', ['--force' => true]);

        $this->info('✓ Database setup completed successfully!');
        $this->info('You can now access the application.');
        
        return 0;
    }
}
