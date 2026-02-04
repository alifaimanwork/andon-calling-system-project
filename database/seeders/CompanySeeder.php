<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $companies = [[
            'name' => 'IAV',
            'code' => 'IAV',
            'logo' => null,
        ], [
            'name' => 'FINE COMPONENT',
            'code' => 'FINE',
            'logo' => null,
        ], [
            'name' => 'SMI BERANANG',
            'code' => 'SMI',
            'logo' => null,
        ]];
        foreach ($companies as $company) {
            (new Company($company))->saveQuietly(); //save without trigger observer events
        }
    }
}
