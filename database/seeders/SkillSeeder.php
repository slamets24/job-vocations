<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('skills')->insert([
            ['skill'  => 'Pemrograman Python'],
            ['skill' => 'Pemrograman JavaScript'],
            ['skill' => 'Pemrograman Java'],
            ['skill' => 'Pemrograman C#'],
            ['skill' => 'Rekayasa Perangkat Lunak'],
            ['skill' => 'Desain UI/UX'],
            ['skill' => 'Database (MySQL, PostgreSQL)'],
            ['skill' => 'Cloud Computing (AWS, GCP)'],
            ['skill' => 'Machine Learning'],
            ['skill' => 'Cybersecurity'],
        ]);
    }
}
