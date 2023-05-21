<?php

namespace Database\Seeders;

use App\Models\BaharNumber;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ResultMaster;

class DatabaseSeeder extends Seeder
{

    public function run()
    {

        $this->call(UserTypeSeeder::class);
        $this->call(PayOutSlabSeeder::class);
        $this->call(CardCombinationTypeSeeder::class);
        $this->call(CardCombinationSeeder::class);
        $this->call(SingleNumberSeeder::class);
//        $this->call(NumberCombinationSeeder::class);
        $this->call(GameSeeder::class);
        $this->call(DrawMasterSeeder::class);
        $this->call(GameTypeSeeder::class);
        $this->call(NextGameDrawSeeder::class);
        $this->call(AndarNumberSeeder::class);
        $this->call(BaharNumberSeeder::class);
        $this->call(DoubleNumberCombinationSeeder::class);



        User::create(['user_name'=>'Arindam Biswas','email'=>'1001','password'=>"b8c37e33defde51cf91e1e03e51657da",'visible_password' => '1001' ,'mobile1'=>'9836444999','pay_out_slab_id'=>1, 'user_type_id'=>1,'closing_balance' => 5000]);
        User::create(['user_name'=>'Ananda Sen','email'=>'1002','password'=>"fba9d88164f3e2d9109ee770223212a0",'visible_password' => '1002' ,'mobile1'=>'9536485201','pay_out_slab_id'=>1,'user_type_id'=>2,'closing_balance' => 5000]);
//        User::create(['user_name'=>'Mahesh Roy','email'=>'1003','password'=>"aa68c75c4a77c87f97fb686b2f068676",'mobile1'=>'8532489030','pay_out_slab_id'=>1,'user_type_id'=>3,'closing_balance' => 5000]);
//        User::create(['user_name'=>'Ramesh Ghosh','email'=>'1004','password'=>"fed33392d3a48aa149a87a38b875ba4a",'mobile1'=>'9587412358','pay_out_slab_id'=>1,'user_type_id'=>4,'closing_balance' => 5000]);

        //resultMaster
//        ResultMaster::insert([
//            ['draw_master_id'=>1,'number_combination_id'=>54,'game_id'=>1,'game_date'=>'2021-12-25'],
//            ['draw_master_id'=>2,'number_combination_id'=>11,'game_id'=>3,'game_date'=>'2021-05-24'],
//            ['draw_master_id'=>3,'number_combination_id'=>65,'game_id'=>2,'game_date'=>'2021-05-24'],
//            ['draw_master_id'=>4,'number_combination_id'=>55,'game_id'=>2,'game_date'=>'2021-05-24'],
//            ['draw_master_id'=>5,'number_combination_id'=>37,'game_id'=>4,'game_date'=>'2021-05-24'],
//
//            ['draw_master_id'=>1,'number_combination_id'=>44,'game_id'=>4,'game_date'=>'2021-05-23'],
//            ['draw_master_id'=>2,'number_combination_id'=>11,'game_id'=>2,'game_date'=>'2021-05-23'],
//            ['draw_master_id'=>3,'number_combination_id'=>15,'game_id'=>1,'game_date'=>'2021-05-23'],
//            ['draw_master_id'=>4,'number_combination_id'=>55,'game_id'=>3,'game_date'=>'2021-05-23'],
//            ['draw_master_id'=>5,'number_combination_id'=>17,'game_id'=>1,'game_date'=>'2021-05-23'],
//            ['draw_master_id'=>6,'number_combination_id'=>47,'game_id'=>1,'game_date'=>'2021-05-23'],
//            ['draw_master_id'=>7,'number_combination_id'=>15,'game_id'=>2,'game_date'=>'2021-05-23'],
//            ['draw_master_id'=>8,'number_combination_id'=>86,'game_id'=>1,'game_date'=>'2021-05-23'],
//        ]);

    }
}
