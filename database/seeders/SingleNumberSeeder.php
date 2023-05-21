<?php

namespace Database\Seeders;

use App\Models\SingleNumber;
use Illuminate\Database\Seeder;

class SingleNumberSeeder extends Seeder
{
    public function run()
    {
        SingleNumber::insert([
            ['single_name' => 'Shree Yantra','single_number' => 2001, 'single_order' => 1],
            ['single_name' => 'Vashikaran Yantra','single_number' => 2002, 'single_order' => 2],
            ['single_name' => 'Sudarshan Yantra','single_number' => 2003, 'single_order' => 3],
            ['single_name' => 'Vastu Yantra','single_number' => 2004, 'single_order' => 4],
            ['single_name' => 'Planet Yantra','single_number' => 2005, 'single_order' => 5],
            ['single_name' => 'Love Yantra','single_number' => 2006, 'single_order' => 6],
            ['single_name' => 'Tara Yantra','single_number' => 2007, 'single_order' => 7],
            ['single_name' => 'Grah Yantra','single_number' => 2008, 'single_order' => 8],
            ['single_name' => 'Matsya Yantra','single_number' => 2009, 'single_order' => 9],
            ['single_name' => 'Meditation Yantra','single_number' => 2010, 'single_order' => 10]
        ]);
    }
}
