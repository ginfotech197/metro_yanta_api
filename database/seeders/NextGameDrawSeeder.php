<?php

namespace Database\Seeders;

use App\Models\NextGameDraw;
use Illuminate\Database\Seeder;

class NextGameDrawSeeder extends Seeder
{
    public function run()
    {
        NextGameDraw::create(['next_draw_id' => 2, 'last_draw_id' => 1, 'game_id'=>1]);
//        NextGameDraw::create(['next_draw_id' => 722, 'last_draw_id' => 721, 'game_id'=>2]);
//        NextGameDraw::create(['next_draw_id' => 1442, 'last_draw_id' => 1441, 'game_id'=>3]);
//        NextGameDraw::create(['next_draw_id' => 2162, 'last_draw_id' => 2161, 'game_id'=>4]);
//        NextGameDraw::create(['next_draw_id' => 2882, 'last_draw_id' => 2881, 'game_id'=>5]);
    }
}
