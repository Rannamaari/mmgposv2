<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Customer, Motorcycle, Service, Part};

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Customers + Bikes
        $c1 = Customer::firstOrCreate(['phone'=>'7700001'], ['name'=>'Ali','phone'=>'7700001']);
        $c2 = Customer::firstOrCreate(['phone'=>'7600002'], ['name'=>'Hussain','phone'=>'7600002']);
        $c3 = Customer::firstOrCreate(['phone'=>'7900003'], ['name'=>'Reehan','phone'=>'7900003']);

        foreach ([
            [$c1,'AA-1234','Wave125S'],
            [$c1,'AA-5678','Airblade'],
            [$c2,'BB-1001','Exciter'],
            [$c2,'BB-2002','PCX'],
            [$c3,'CC-3003','Scoopy'],
        ] as [$cust,$plate,$model]) {
            Motorcycle::firstOrCreate(
                ['customer_id'=>$cust->id,'plate_no'=>$plate],
                ['customer_id'=>$cust->id,'plate_no'=>$plate,'model'=>$model]
            );
        }

        // Services
        $services = [
            ['name'=>'Battery Charge','default_price'=>50,'category'=>'Diagnosis','is_active'=>true],
            ['name'=>'Jump Start','default_price'=>50,'category'=>'Diagnosis','is_active'=>true],
            ['name'=>'Full Service','default_price'=>500,'category'=>'Maintenance','is_active'=>true],
            ['name'=>'Clean Engine','default_price'=>250,'category'=>'Maintenance','is_active'=>true],
            ['name'=>'Shock Oil Change & Repair','default_price'=>350,'category'=>'Suspension','is_active'=>true],
            ['name'=>'Motorcycle Overhaul (Wave125s)','default_price'=>0,'category'=>'Overhaul','is_active'=>true],
            ['name'=>'Motorcycle Overhaul (Airblade)','default_price'=>0,'category'=>'Overhaul','is_active'=>true],
        ];
        foreach ($services as $s) { 
            Service::firstOrCreate(['name' => $s['name']], $s);
        }

        // Parts
        $parts = [
            ['sku'=>'VISOR-BLK','name'=>'Visor','price'=>450,'cost'=>300,'stock_qty'=>15,'is_active'=>true],
            ['sku'=>'SPARK-PLUG','name'=>'Spark Plug','price'=>130,'cost'=>90,'stock_qty'=>50,'is_active'=>true],
            ['sku'=>'REAR-HC','name'=>'Rear head cover','price'=>686,'cost'=>500,'stock_qty'=>8,'is_active'=>true],
            ['sku'=>'HANDLE-BAR','name'=>'Handle bar','price'=>780,'cost'=>600,'stock_qty'=>6,'is_active'=>true],
            ['sku'=>'TIRE-STD','name'=>'Tire','price'=>750,'cost'=>520,'stock_qty'=>20,'is_active'=>true],
            ['sku'=>'MIRROR-STD','name'=>'Side mirror','price'=>150,'cost'=>90,'stock_qty'=>30,'is_active'=>true],
        ];
        foreach ($parts as $p) { 
            Part::firstOrCreate(['sku' => $p['sku']], $p);
        }

        // Walk-in customer
        $walkIn = Customer::firstOrCreate(
            ['name' => 'Walk-in'],
            ['phone' => null]
        );
        
        Motorcycle::firstOrCreate(
            ['customer_id' => $walkIn->id, 'plate_no' => 'N/A'],
            ['model' => 'Unknown']
        );
    }
}