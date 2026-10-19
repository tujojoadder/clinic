<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['clinic_name'=>'Clinic Care','clinic_address'=>'উল্লাপাড়া, সিরাজগঞ্জ','clinic_phone'=>'','currency'=>'BDT'] as $key=>$value) {
            Setting::firstOrCreate(['key'=>$key], ['value'=>$value]);
        }
    }
}
