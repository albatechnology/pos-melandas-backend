<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range('A', 'C') as $letter) {
            Company::factory()->create(["name" => 'Company ' . $letter]);
        }
    }
}
