<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Income Categories
            ['name' => 'Gaji', 'type' => 'income', 'color' => '#10B981', 'icon' => 'money'],
            ['name' => 'Bonus', 'type' => 'income', 'color' => '#059669', 'icon' => 'gift'],
            ['name' => 'Investasi', 'type' => 'income', 'color' => '#34D399', 'icon' => 'trending-up'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#6EE7B7', 'icon' => 'briefcase'],
            ['name' => 'Lainnya', 'type' => 'income', 'color' => '#A7F3D0', 'icon' => 'plus'],

            // Expense Categories
            ['name' => 'Makanan', 'type' => 'expense', 'color' => '#EF4444', 'icon' => 'utensils'],
            ['name' => 'Transportasi', 'type' => 'expense', 'color' => '#F97316', 'icon' => 'car'],
            ['name' => 'Belanja', 'type' => 'expense', 'color' => '#F59E0B', 'icon' => 'shopping-bag'],
            ['name' => 'Tagihan', 'type' => 'expense', 'color' => '#DC2626', 'icon' => 'file-text'],
            ['name' => 'Hiburan', 'type' => 'expense', 'color' => '#7C3AED', 'icon' => 'music'],
            ['name' => 'Kesehatan', 'type' => 'expense', 'color' => '#EC4899', 'icon' => 'heart'],
            ['name' => 'Pendidikan', 'type' => 'expense', 'color' => '#3B82F6', 'icon' => 'book'],
            ['name' => 'Investasi', 'type' => 'expense', 'color' => '#8B5CF6', 'icon' => 'trending-up'],
            ['name' => 'Lainnya', 'type' => 'expense', 'color' => '#6B7280', 'icon' => 'more-horizontal'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
