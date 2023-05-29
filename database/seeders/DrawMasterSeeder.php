<?php

namespace Database\Seeders;

use App\Models\DrawMaster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrawMasterSeeder extends Seeder
{
    public function run()
    {

//        $sql = file_get_contents(database_path() . '/seeds/draw_master.sql');
//
//        DB::statement($sql);

       DrawMaster::insert([



        ['draw_name'=>'Vastu Yantra','start_time'=>'08:15:00','end_time'=>'08:30:00', 'visible_time'=>'08:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vastu Yantra','start_time'=>'08:30:00','end_time'=>'08:45:00', 'visible_time'=>'08:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Sudashan Yantra','start_time'=>'08:45:00','end_time'=>'09:00:00', 'visible_time'=>'09:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Grah Yantra','start_time'=>'09:00:00','end_time'=>'09:15:00', 'visible_time'=>'09:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Grah Yantra','start_time'=>'09:15:00','end_time'=>'09:30:00', 'visible_time'=>'09:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Sudashan Yantra','start_time'=>'09:30:00','end_time'=>'09:45:00', 'visible_time'=>'09:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vashikaran Yantra','start_time'=>'09:45:00','end_time'=>'10:00:00', 'visible_time'=>'10:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Matsya Yantra','start_time'=>'10:00:00','end_time'=>'10:15:00', 'visible_time'=>'10:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Tara Yantra','start_time'=>'10:15:00','end_time'=>'10:30:00', 'visible_time'=>'10:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Shree Yantra','start_time'=>'10:30:00','end_time'=>'10:45:00', 'visible_time'=>'10:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Matsya Yantra','start_time'=>'10:45:00','end_time'=>'11:00:00', 'visible_time'=>'11:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Matsya Yantra','start_time'=>'11:00:00','end_time'=>'11:15:00', 'visible_time'=>'11:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Sudarshan Yantra','start_time'=>'11:15:00','end_time'=>'11:30:00', 'visible_time'=>'11:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Love Yantra','start_time'=>'11:30:00','end_time'=>'11:45:00', 'visible_time'=>'11:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Planet Yantra','start_time'=>'11:45:00','end_time'=>'12:00:00', 'visible_time'=>'12:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vastu Yantra','start_time'=>'12:00:00','end_time'=>'12:15:00', 'visible_time'=>'12:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Planet Yantra','start_time'=>'12:15:00','end_time'=>'12:30:00', 'visible_time'=>'12:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Meditation Yantra','start_time'=>'12:30:00','end_time'=>'12:45:00', 'visible_time'=>'12:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Shree Yantra','start_time'=>'12:45:00','end_time'=>'01:00:00', 'visible_time'=>'01:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Grah Yantra','start_time'=>'01:00:00','end_time'=>'01:15:00', 'visible_time'=>'01:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Meditation Yantra','start_time'=>'01:15:00','end_time'=>'01:30:00', 'visible_time'=>'01:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vastu Yantra','start_time'=>'01:30:00','end_time'=>'01:45:00', 'visible_time'=>'01:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vashikaran Yantra','start_time'=>'01:45:00','end_time'=>'02:00:00', 'visible_time'=>'02:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Shree Yantra','start_time'=>'02:00:00','end_time'=>'02:15:00', 'visible_time'=>'02:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Sudarshan Yantra','start_time'=>'02:15:00','end_time'=>'02:30:00', 'visible_time'=>'02:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Planet Yantra','start_time'=>'02:30:00','end_time'=>'02:45:00', 'visible_time'=>'02:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Sudarshan Yantra','start_time'=>'02:45:00','end_time'=>'03:00:00', 'visible_time'=>'03:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vashikaran Yantra','start_time'=>'03:00:00','end_time'=>'03:15:00', 'visible_time'=>'03:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Love Yantra','start_time'=>'03:15:00','end_time'=>'03:30:00', 'visible_time'=>'03:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Love Yantra','start_time'=>'03:30:00','end_time'=>'03:45:00', 'visible_time'=>'03:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Planet Yantra','start_time'=>'03:45:00','end_time'=>'04:00:00', 'visible_time'=>'04:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Planet Yantra','start_time'=>'04:00:00','end_time'=>'04:15:00', 'visible_time'=>'04:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Planet Yantra','start_time'=>'04:15:00','end_time'=>'04:30:00', 'visible_time'=>'04:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Sudarshan Yantra','start_time'=>'04:30:00','end_time'=>'04:45:00', 'visible_time'=>'04:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Tara Yantra','start_time'=>'04:45:00','end_time'=>'05:00:00', 'visible_time'=>'05:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Matsya Yantra','start_time'=>'05:00:00','end_time'=>'05:15:00', 'visible_time'=>'05:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Shree Yantra','start_time'=>'05:15:00','end_time'=>'05:30:00', 'visible_time'=>'05:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Matsya Yantra','start_time'=>'05:30:00','end_time'=>'05:45:00', 'visible_time'=>'05:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Shree Yantra','start_time'=>'05:45:00','end_time'=>'06:00:00', 'visible_time'=>'06:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Grah Yantra','start_time'=>'06:00:00','end_time'=>'06:15:00', 'visible_time'=>'06:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Grah Yantra','start_time'=>'06:15:00','end_time'=>'06:30:00', 'visible_time'=>'06:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Tara Yantra','start_time'=>'06:30:00','end_time'=>'06:45:00', 'visible_time'=>'06:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Tara Yantra','start_time'=>'06:45:00','end_time'=>'07:00:00', 'visible_time'=>'07:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Matsya Yantra','start_time'=>'07:00:00','end_time'=>'07:15:00', 'visible_time'=>'07:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Love Yantra','start_time'=>'07:15:00','end_time'=>'07:30:00', 'visible_time'=>'07:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Meditation Yantra','start_time'=>'07:30:00','end_time'=>'07:45:00', 'visible_time'=>'07:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Shree Yantra','start_time'=>'07:45:00','end_time'=>'08:00:00', 'visible_time'=>'08:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Shree Yantra','start_time'=>'08:00:00','end_time'=>'08:15:00', 'visible_time'=>'08:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vastu Yantra','start_time'=>'08:15:00','end_time'=>'08:30:00', 'visible_time'=>'08:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Love Yantra','start_time'=>'08:30:00','end_time'=>'08:45:00', 'visible_time'=>'08:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Vashikaran Yantra','start_time'=>'08:45:00','end_time'=>'09:00:00', 'visible_time'=>'09:00','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Love Yantra','start_time'=>'09:00:00','end_time'=>'09:15:00', 'visible_time'=>'09:15','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Sudarshan Yantra','start_time'=>'09:15:00','end_time'=>'09:30:00', 'visible_time'=>'09:30','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Matsya Yantra','start_time'=>'09:30:00','end_time'=>'09:45:00', 'visible_time'=>'09:45','game_id'=>'1','active'=>'0'],
        ['draw_name'=>'Grah Yantra','start_time'=>'09:45:00','end_time'=>'10:00:00', 'visible_time'=>'10:00','game_id'=>'1','active'=>'0'],



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
       ]);
    }
}


// Tara Yantra
// Shree Yantra
// Matsya Yantra
// Matsya Yantra
// Sudarshan Yantra
// Love Yantra
// Planet Yantra
// Vastu Yantra
// Planet Yantra
// Meditation Yantra
// Shree Yantra
// Grah Yantra
// Meditation Yantra
// Vastu Yantra
// Vashikaran Yantra
// Shree Yantra
// Sudarshan Yantra
// Planet Yantra
// Sudarshan Yantra
// Vashikaran Yantra
// Love Yantra
// Love Yantra
// Planet Yantra
// Planet Yantra
// Planet Yantra
// Sudarshan Yantra
// Tara Yantra
// Matsya Yantra
// Shree Yantra
// Matsya Yantra
// Shree Yantra
// Grah Yantra
// Grah Yantra
// Tara Yantra
// Tara Yantra
// Matsya Yantra
// Love Yantra
// Meditation Yantra
// Shree Yantra
// Shree Yantra
// Vastu Yantra
// Love Yantra
// Vashikaran Yantra
// Love Yantra
// Sudarshan Yantra
// Matsya Yantra
// Grah Yantra
