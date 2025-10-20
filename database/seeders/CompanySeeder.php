<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'PT Cloud Hosting Indonesia',
            'logo' => 'null',
            'address' => 'Sentral Senayan II, Jl. Asia Afrika No.8, RT.1/RW.3, Gelora, Kecamatan Tanah Abang, Jakarta 10270',
            'phone' => '081234567890',
        ]);

        Company::create([
            'name' => 'RumahWeb Indonesia',
            'logo' => 'null',
            'address' => 'Jl. Sidomulyo No. 6 Condong Catur
Depok, Sleman, Yogyakarta 55281',
            'phone' => '0274882257',
        ]);
    }
}
