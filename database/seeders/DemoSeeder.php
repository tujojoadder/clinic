<?php

namespace Database\Seeders;

use App\Models\{Patient,Doctor,Bed,DiagnosticTest,TestParameter};
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $doctor=Doctor::firstOrCreate(['name'=>'Demo Doctor'], ['specialty'=>'Medicine','fee'=>500,'followup_fee'=>300,'active'=>1]);
        Bed::firstOrCreate(['name'=>'Demo Cabin 101'], ['ward'=>'Cabin','type'=>'cabin','daily_rate'=>1500,'state'=>'available']);
        Patient::firstOrCreate(['name'=>'Demo Patient','phone'=>'01700000001'], ['age_years'=>32,'gender'=>'female','address'=>'Demo address']);
        $test=DiagnosticTest::firstOrCreate(['name'=>'Demo CBC'], ['sample_type'=>'Blood','price'=>500,'turnaround_hours'=>4,'active'=>1]);
        TestParameter::firstOrCreate(['test_id'=>$test->id,'name'=>'Hemoglobin'], ['unit'=>'g/dL','reference_range'=>'Enter verified lab range','sort_order'=>1]);
    }
}
