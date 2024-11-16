<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'خيال علمي',
                'category_name_en' => 'Science fiction'
            ],
            [
                'category_name' => 'روايات وقصص',
                'category_name_en' => 'Novels and stories'
            ],
            [
                'category_name' => 'تاريخ',
                'category_name_en' => 'History'
            ],
            [
                'category_name' => 'صحة',
                'category_name_en' => 'Health'
            ],
            [
                'category_name' => 'تطوير ذات',
                'category_name_en' => 'Self development'
            ],
            [
                'category_name' => 'طبخ',
                'category_name_en' => 'Cooking'
            ],
            [
                'category_name' => 'أطفال',
                'category_name_en' => 'Children'
            ],
            [
                'category_name' => 'سفر',
                'category_name_en' => 'Travel'
            ],
            [
                'category_name' => 'كتب مضحكة',
                'category_name_en' => 'Funny books'
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'category_name' => $category['category_name'],
                'category_name_en' => $category['category_name_en']
            ]);
        }
    }
}
