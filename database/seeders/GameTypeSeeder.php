<?php

namespace Database\Seeders;

use App\Models\GameType;
use Illuminate\Database\Seeder;

class GameTypeSeeder extends Seeder
{
    public function run()
    {
        GameType::insert([
            ['game_type_name'=>'single','game_id' => 1 ,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>9, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'triple','game_id' => 1,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>900, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'12-Card','game_id' => 2,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>10, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'16-Card','game_id' => 3,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>14, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'double','game_id' => 1,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>90, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'single individual','game_id' => 4,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>9, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'double individual','game_id' => 5,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>90, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'Andar','game_id' => 5,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>9, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'Bahar','game_id' => 5,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>9, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100],
//            ['game_type_name'=>'Rollet','game_id' => 6,'game_type_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>36, 'winning_bonus_percent'=>0.2, 'commission'=>0.00, 'payout'=>100,'default_payout'=>100]
        ]);
    }
}
