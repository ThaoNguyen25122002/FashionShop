<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(1)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test1@example.com',
        //     'role' => 'admin'
        // ]);
        // Category::factory()->create([
        //     'name' => 'Áo Nam',
        //     'sort' => 1,
        //     'role' => true
        // ]);
        $categories
        = [
            ['name' => 'Áo sơ mi', 'sort' => 1, 'is_show' => 1],
            ['name' => 'Áo thun', 'sort' => 2, 'is_show' => 1],
            ['name' => 'Áo len', 'sort' => 3, 'is_show' => 1],
            ['name' => 'Áo khoác', 'sort' => 4, 'is_show' => 1],
            ['name' => 'Áo vest', 'sort' => 5, 'is_show' => 1],
            ['name' => 'Quần jeans', 'sort' => 6, 'is_show' => 1],
            ['name' => 'Quần tây', 'sort' => 7, 'is_show' => 1],
            ['name' => 'Quần short', 'sort' => 8, 'is_show' => 1],
            ['name' => 'Quần jogger', 'sort' => 9, 'is_show' => 1],
            ['name' => 'Đầm maxi', 'sort' => 10, 'is_show' => 1],
        ];
        Category::insert($categories);
    }
}
