<?php

namespace App\Http\Controllers;

use App\Http\Resources\SingleNumbers;
use App\Models\AndarNumber;
use App\Models\BaharNumber;
use App\Models\DoubleNumberCombination;
use App\Models\NumberCombination;
use App\Models\SingleNumber;
use Illuminate\Http\Request;
use App\Http\Resources\NumberCombinationsResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NumberCombinationController extends Controller
{
    public function index()
    {
        $result = NumberCombination::get();
        return response()->json(['success'=>1,'data'=> NumberCombinationsResource::collection($result)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function getNumbersBySingleNumber($id){
        $result = NumberCombination::where('single_number_id',$id)->get();
        return response()->json(['success'=>1,'data'=> NumberCombinationsResource::collection($result)], 200,[],JSON_NUMERIC_CHECK);
    }
    public function getAllInMatrix(){

        $numberCombinations = Cache::remember('get_all_number_combinations', 3000000, function () {
            return DB::select("select id, single_number_id, triple_number, visible_triple_number from number_combinations");
        });

        return response()->json(['success'=>1,'data'=> NumberCombinationsResource::collection($numberCombinations)], 200);
    }

    public function create_migration(){

        for($i=0; $i<1000; $i++){
            $data = str_pad($i,3,'0', STR_PAD_LEFT);
            $x = str_split($data);
            $singleNumberId = (SingleNumber::whereSingleNumber($x[2])->first())->id;

            $numberCombination = new NumberCombination();
            $numberCombination->single_number_id = $singleNumberId;
            $numberCombination->triple_number = $data;
            $numberCombination->visible_triple_number = $data;
            $numberCombination->save();

            if($i<100){
                $data1 = str_pad($i,2,'0', STR_PAD_LEFT);
                $doubleNumberCombination = new DoubleNumberCombination();
                $doubleNumberCombination->single_number_id = $singleNumberId;
                $doubleNumberCombination->double_number = $data1;
                $doubleNumberCombination->visible_double_number = $data1;
                $doubleSplit = str_split($data1);
                $doubleNumberCombination->andar_number_id = (AndarNumber::whereAndarNumber($doubleSplit[0])->first())->id;
                $doubleNumberCombination->bahar_number_id = (BaharNumber::whereBaharNumber($doubleSplit[1])->first())->id;
                $doubleNumberCombination->save();
            }
        }

        return response()->json(['success'=>1,'data'=> "Successfully added"], 200,[],JSON_NUMERIC_CHECK);
    }
}
