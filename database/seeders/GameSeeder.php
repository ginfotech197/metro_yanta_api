<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run()
    {
        Game::insert([
//            ['game_name'=> 'TRIPLE CHANCE'],
//            ['game_name'=> 'TRIPLE CHANCE'],
//            ['game_name'=> '12 CARD'],
//            ['game_name'=> '16 CARD',],
            ['game_name'=> 'SINGLE'],
//            ['game_name'=> 'DOUBLE'],
//            ['game_name'=> 'ROLLET'],
        ]);
    }
}
