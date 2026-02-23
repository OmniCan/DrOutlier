<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $modules = [
            [
                'name' => 'notes',
                'display_name' => 'Notes',
                'slug' => 'notes',
                'frontend_url' => '/notes',
                'admin_url' => '/admin/note',
                'description' => 'Access to study notes and materials',
                'icon' => 'fas fa-book',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'spotters',
                'display_name' => 'Spotters',
                'slug' => 'spotters',
                'frontend_url' => '/spotters',
                'admin_url' => '/admin/spotters',
                'description' => 'Access to spotter questions and materials',
                'icon' => 'fas fa-eye',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'osce',
                'display_name' => 'OSCE',
                'slug' => 'osce',
                'frontend_url' => '/osce',
                'admin_url' => '/admin/osce',
                'description' => 'Access to OSCE practice materials',
                'icon' => 'fas fa-stethoscope',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ai_rad',
                'display_name' => 'AI Rad (Munchies)',
                'slug' => 'ai-rad',
                'frontend_url' => '/ai-rad',
                'admin_url' => '/admin/munchies',
                'description' => 'Access to AI Radiology content',
                'icon' => 'fas fa-brain',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'practical_essentials',
                'display_name' => 'Practical Essentials',
                'slug' => 'practical-essentials',
                'frontend_url' => '/practical-essentials',
                'admin_url' => '/admin/basic',
                'description' => 'Access to practical essentials and basics',
                'icon' => 'fas fa-hands-helping',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'watch_and_learn',
                'display_name' => 'Watch and Learn',
                'slug' => 'watch-and-learn',
                'frontend_url' => '/watch-and-learn',
                'admin_url' => '/admin/watch-and-learn',
                'description' => 'Access to video learning materials',
                'icon' => 'fas fa-video',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'quizora',
                'display_name' => 'Quizora',
                'slug' => 'quizora',
                'frontend_url' => '/quizora',
                'admin_url' => '/admin/quiz',
                'description' => 'Access to quiz and practice questions',
                'icon' => 'fas fa-question-circle',
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('modules')->insert($modules);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('modules')->whereIn('slug', [
            'notes',
            'spotters',
            'osce',
            'ai-rad',
            'practical-essentials',
            'watch-and-learn',
            'quizora'
        ])->delete();
    }
};
