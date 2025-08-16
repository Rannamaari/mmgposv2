<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        Service::insert([
            // Diagnosis / Quick jobs
            ['name'=>'Battery Charge', 'default_price'=>50, 'category'=>'Diagnosis', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Jump Start', 'default_price'=>50, 'category'=>'Diagnosis', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],

            // Overhauls (price will vary, set when adding to Work Order)
            ['name'=>'Motorcycle Overhaul (Wave125s)', 'default_price'=>0, 'category'=>'Overhaul', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Motorcycle Overhaul (Airblade)',  'default_price'=>0, 'category'=>'Overhaul', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],

            // Common garage services (handy for day-one use)
            ['name'=>'Full Service', 'default_price'=>500, 'category'=>'Maintenance', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Clean Engine', 'default_price'=>250, 'category'=>'Maintenance', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Shock Oil Change & Repair', 'default_price'=>350, 'category'=>'Suspension', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],
            ['name'=>'Install Coolant & Rollerset', 'default_price'=>250, 'category'=>'Installation', 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now],
        ]);
    }
}
