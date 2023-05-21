<?php

namespace Database\Seeders;

use App\Models\PayOutSlab;
use Illuminate\Database\Seeder;

class PayOutSlabSeeder extends Seeder
{
    public function run()
    {
        PayOutSlab::insert([
            ['slab_range' => '100-116', 'slab_value' =>116 ,'slab_status' => 'Very high'],
            ['slab_range' => '116-132', 'slab_value' =>132 ,'slab_status' => 'High'],
            ['slab_range' => '132-148', 'slab_value' =>148 ,'slab_status' => 'Medium'],
            ['slab_range' => '148-164', 'slab_value' =>164 ,'slab_status' => 'Low'],
            ['slab_range' => '164-180', 'slab_value' =>180 ,'slab_status' => 'Very low'],
        ]);
    }
}
