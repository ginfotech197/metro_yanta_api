<?php

namespace Database\Seeders;

use App\Models\DrawMaster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrawMasterSeeder extends Seeder
{
    public function run()
    {

        $sql = file_get_contents(database_path() . '/seeds/draw_master.sql');

        DB::statement($sql);

//        DrawMaster::insert([
//
//            ['draw_name'=> 'Good morning','start_time'=>'00:00 ','end_time'=>'10:00','visible_time'=>'10:00 am','game_id'=>1,'active'=>1],
//            ['draw_name'=> 'Good morning','start_time'=>'10:00 ','end_time'=>'11:30','visible_time'=>'11:30 am','game_id'=>1,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'11:30','end_time'=>'13:00','visible_time'=>'01:00 pm','game_id'=>1,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'13:00','end_time'=>'14:30','visible_time'=>'02:30 pm','game_id'=>1,'active'=>0],
//            ['draw_name'=> 'Good afternoon','start_time'=>'14:30','end_time'=>'16:00','visible_time'=>'04:00 pm','game_id'=>1,'active'=>0],
//            ['draw_name'=> 'Good evening','start_time'=>'16:00','end_time'=>'17:30','visible_time'=>'05:30 pm','game_id'=>1,'active'=>0],
//            ['draw_name'=> 'Lets play','start_time'=>'17:30','end_time'=>'19:00','visible_time'=>'07:00 pm','game_id'=>1,'active'=>0],
//            ['draw_name'=> 'Good night','start_time'=>'19:00','end_time'=>'20:30','visible_time'=>'08:30 pm','game_id'=>1,'active'=>0],
//
//
//            ['draw_name'=> 'Good morning','start_time'=>'00:00 ','end_time'=>'10:30','visible_time'=>'10:30 am','game_id'=>2,'active'=>1],
//            ['draw_name'=> 'Good morning','start_time'=>'10:30 ','end_time'=>'12:00','visible_time'=>'12:00 pm','game_id'=>2,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'12:00','end_time'=>'13:30','visible_time'=>'01:30 pm','game_id'=>2,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'13:30','end_time'=>'15:00','visible_time'=>'03:00 pm','game_id'=>2,'active'=>0],
//            ['draw_name'=> 'Good afternoon','start_time'=>'15:00','end_time'=>'16:30','visible_time'=>'04:30 pm','game_id'=>2,'active'=>0],
//            ['draw_name'=> 'Good evening','start_time'=>'16:30','end_time'=>'18:30','visible_time'=>'06:30 pm','game_id'=>2,'active'=>0],
//            ['draw_name'=> 'Lets play','start_time'=>'18:30','end_time'=>'19:30','visible_time'=>'07:30 pm','game_id'=>2,'active'=>0],
//            ['draw_name'=> 'Good night','start_time'=>'07:30','end_time'=>'21:00','visible_time'=>'09:00 pm','game_id'=>2,'active'=>0],
//
//
//
//            ['draw_name'=> 'Good morning','start_time'=>'08:30 ','end_time'=>'09:00','visible_time'=>'09:00 am','game_id'=>3,'active'=>1],
//            ['draw_name'=> 'Good morning','start_time'=>'09:00','end_time'=>'09:30','visible_time'=>'09:30 am','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'09:30','end_time'=>'10:00','visible_time'=>'10:00 am','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Good afternoon','start_time'=>'10:00','end_time'=>'10:30','visible_time'=>'10:30 am','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Good evening','start_time'=>'10:30','end_time'=>'11:00','visible_time'=>'11:00 pm','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Lets play','start_time'=>'11:00','end_time'=>'11:30','visible_time'=>'11:30 pm','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Good night','start_time'=>'11:30','end_time'=>'12:00','visible_time'=>'12:00 pm','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'12:00','end_time'=>'12:30','visible_time'=>'12:30 pm','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Good afternoon','start_time'=>'12:30','end_time'=>'13:00','visible_time'=>'01:00 pm','game_id'=>3,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'13:00 ','end_time'=>'13:30','visible_time'=>'01:30 pm','game_id'=>3,'active'=>0],
//
//            ['draw_name'=> 'Good morning','start_time'=>'08:30 ','end_time'=>'09:00','visible_time'=>'09:00 am','game_id'=>4,'active'=>1],
//            ['draw_name'=> 'Good morning','start_time'=>'09:00','end_time'=>'09:30','visible_time'=>'09:30 am','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'09:30','end_time'=>'10:00','visible_time'=>'10:00 am','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Good afternoon','start_time'=>'10:00','end_time'=>'10:30','visible_time'=>'10:30 am','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Good evening','start_time'=>'10:30','end_time'=>'11:00','visible_time'=>'11:00 pm','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Lets play','start_time'=>'11:00','end_time'=>'11:30','visible_time'=>'11:30 pm','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Good night','start_time'=>'11:30','end_time'=>'12:00','visible_time'=>'12:00 pm','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'12:00','end_time'=>'12:30','visible_time'=>'12:30 pm','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Good afternoon','start_time'=>'12:30','end_time'=>'13:00','visible_time'=>'01:00 pm','game_id'=>4,'active'=>0],
//            ['draw_name'=> 'Good morning','start_time'=>'13:00 ','end_time'=>'13:30','visible_time'=>'01:30 pm','game_id'=>4,'active'=>0],
//
//        ]);
    }
}
