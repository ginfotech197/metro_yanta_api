<?php

namespace App\Http\Controllers;

use App\Http\Resources\ManualResultResource;
use App\Models\DoubleNumberCombination;
use App\Models\DrawMaster;
use App\Models\GameType;
use App\Models\ManualResult;
use App\Models\NumberCombination;
use App\Models\ResultMaster;
use App\Models\SingleNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ManualResultController extends Controller
{

    public function index()
    {
        //
    }

    public function save_manual_result(Request $request)
    {
//        $rules = array(
//            'drawMasterId'=>['required','exists:draw_masters,id',
//                    function($attribute, $value, $fail){
//                        $existingManual=ManualResult::where('draw_master_id', $value)->where('game_date','=',Carbon::today())->first();
//                        if($existingManual){
//                            $fail($value.' Duplicate entry');
//                        }
//                    }
//                ],
//            'numberCombinationId'=>'required|exists:number_combinations,id',
//        );
//        $validator = Validator::make($request->all(),$rules);
//
//        if($validator->fails()){
//            return response()->json(['success'=>0,'data'=>null,'error'=>$validator->messages()], 406,[],JSON_NUMERIC_CHECK);
//        }
        $requestedData = (object)$request->json()->all();

//        $drawMasterTemp = DrawMaster::whereGameId($requestedData->gameId)->whereId($requestedData->drawMasterId)->first();
//        if ($drawMasterTemp->is_draw_over === 'yes'){
//
//            $manualResult = new ManualResult();
//            $manualResult->draw_master_id = $requestedData->drawMasterId;
//            $manualResult->number_combination_id = $requestedData->numberCombinationId;
//            $manualResult->game_id = $requestedData->gameId;
//            $manualResult->game_date = Carbon::today();
//            $manualResult->save();
//
//            $resultMaster = new ResultMaster();
//            $resultMaster->draw_master_id = $requestedData->drawMasterId;
//            $resultMaster->number_combination_id = $requestedData->numberCombinationId;
//            $resultMaster->game_id = $requestedData->gameId;
//            $resultMaster->game_date = Carbon::today();
//            $resultMaster->save();
//
//            return response()->json(['success'=>1,'data'=> new ManualResultResource($manualResult)], 200,[],JSON_NUMERIC_CHECK);
//        }else{
//            $manualResult = ManualResult::whereGameId($requestedData->gameId)->whereGameDate(Carbon::today())->first();
//
//            if($manualResult){
////                $manualResult = new ManualResult();
//                $manualResult->draw_master_id = $requestedData->drawMasterId;
//                $manualResult->number_combination_id = $requestedData->numberCombinationId;
//                $manualResult->game_id = $requestedData->gameId;
//                $manualResult->game_date = Carbon::today();
//                $manualResult->update();
//            }else{
//                $manualResult = new ManualResult();
//                $manualResult->draw_master_id = $requestedData->drawMasterId;
//                $manualResult->number_combination_id = $requestedData->numberCombinationId;
//                $manualResult->game_id = $requestedData->gameId;
//                $manualResult->game_date = Carbon::today();
//                $manualResult->save();
//            }
//
//            return response()->json(['success'=>1,'data'=> new ManualResultResource($manualResult)], 200,[],JSON_NUMERIC_CHECK);
//        }

//        DB::beginTransaction();
//        try{
//
//            $manualResult = new ManualResult();
//            $manualResult->draw_master_id = $requestedData->drawMasterId;
//            $manualResult->number_combination_id = $requestedData->numberCombinationId;
//            $manualResult->game_id = $requestedData->gameId;
//            $manualResult->game_date = Carbon::today();
//            $manualResult->save();
//
//            DB::commit();
//        }catch (\Exception $e){
//            DB::rollBack();
//            return response()->json(['success'=>0, 'data' => null, 'error'=>$e->getMessage()], 500);
//        }
//
//        return response()->json(['success'=>1,'data'=> new ManualResultResource($manualResult)], 200,[],JSON_NUMERIC_CHECK);


        $requestedData = $request->json()->all();


        $gameTypeSix = [7,8,9];
        foreach ($requestedData as $data){

            if($data['gameTypeId'] === 7){
                $dataSplit = str_split($data['combinationNumberId']);
                foreach ($gameTypeSix as $newGameType){
                    if($newGameType === 7){
                        $manualResult = new ManualResult();
                        $manualResult->draw_master_id = $data['drawMasterId'];
                        $manualResult->combination_number_id = $data['combinationNumberId'];
                        $manualResult->game_type_id = $newGameType;
                        $manualResult->game_date = Carbon::today();
                        $manualResult->save();
                    }
                    if($newGameType === 8){
                        $manualResult = new ManualResult();
                        $manualResult->draw_master_id = $data['drawMasterId'];
                        $manualResult->combination_number_id = $dataSplit[0];
                        $manualResult->game_type_id = $newGameType;
                        $manualResult->game_date = Carbon::today();
                        $manualResult->save();
                    }
                    if($newGameType === 9){
                        $manualResult = new ManualResult();
                        $manualResult->draw_master_id = $data['drawMasterId'];
                        $manualResult->combination_number_id = $dataSplit[1];
                        $manualResult->game_type_id = $newGameType;
                        $manualResult->game_date = Carbon::today();
                        $manualResult->save();
                    }
                }
            }else if($data['gameTypeId'] === 2){
//                $splitNumber = str_split($tripleData->visible_triple_number)
                $dataCombination = NumberCombination::find($data['combinationNumberId']);

                $splitNumber = str_split($dataCombination->visible_triple_number);
                $singleNumberValue = (SingleNumber::select()->whereSingleNumber($splitNumber[2])->first())->id;
                $doubleNumberValue = (DoubleNumberCombination::select()->whereDoubleNumber($splitNumber[1].$splitNumber[2])->first())->id;

                $manualResult = new ManualResult();
                $manualResult->draw_master_id = $data['drawMasterId'];
                $manualResult->combination_number_id = $singleNumberValue;
                $manualResult->game_type_id = 1;
                $manualResult->game_date = Carbon::today();
                $manualResult->save();

                $manualResult = new ManualResult();
                $manualResult->draw_master_id = $data['drawMasterId'];
                $manualResult->combination_number_id = $data['combinationNumberId'];
                $manualResult->game_type_id = 2;
                $manualResult->game_date = Carbon::today();
                $manualResult->save();

                $manualResult = new ManualResult();
                $manualResult->draw_master_id = $data['drawMasterId'];
                $manualResult->combination_number_id = $doubleNumberValue;
                $manualResult->game_type_id = 5;
                $manualResult->game_date = Carbon::today();
                $manualResult->save();
            }

            else{
                $manualResult = new ManualResult();
                $manualResult->draw_master_id = $data['drawMasterId'];
                $manualResult->combination_number_id = $data['combinationNumberId'];
                $manualResult->game_type_id = $data['gameTypeId'];
                $manualResult->game_date = Carbon::today();
                $manualResult->save();
            }
        }

        return response()->json(['success'=>1,'data'=> $requestedData], 200,[],JSON_NUMERIC_CHECK);

    }

    public function check_total_sale_on_current_draw(Request $request){
        $requestedData = (object)$request->json()->all();

        $returnArray = [];

        $game_id = (GameType::find($requestedData->game_type_id))->game_id;

        $today= Carbon::today()->format('Y-m-d');

        $activeDraw = (DrawMaster::whereGameId($game_id)->whereActive(1)->first())->id;

        if ($game_id == 1){
            $tripleData = NumberCombination::find($requestedData->combination_id);
            $splitedData = str_split($tripleData->visible_triple_number);
            $singleNumberValue = (SingleNumber::select()->whereSingleNumber($splitedData[2])->first())->id;
            $doubleNumberValue = (DoubleNumberCombination::select()->whereDoubleNumber($splitedData[1].$splitedData[2])->first())->id;

            $singleNumber = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$singleNumberValue." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 1 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'single number',
                'quantity' => $singleNumber[0]->quantity,
                'mrp' => $singleNumber[0]->mrp,
                'winning' => $singleNumber[0]->winning_price
            ];

            array_push($returnArray,$x);

            $tripleNumber = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$requestedData->combination_id." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 2 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'triple number',
                'quantity' => $tripleNumber[0]->quantity,
                'mrp' => $tripleNumber[0]->mrp,
                'winning' => $tripleNumber[0]->winning_price
            ];

            array_push($returnArray,$x);

            $doubleNumber = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$doubleNumberValue." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 5 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'double number',
                'quantity' => $doubleNumber[0]->quantity,
                'mrp' => $doubleNumber[0]->mrp,
                'winning' => $doubleNumber[0]->winning_price
            ];
            array_push($returnArray,$x);
        }

        if($game_id == 2){
            $twelveCard = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$requestedData->combination_id." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 3 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'Twelve Card',
                'quantity' => $twelveCard[0]->quantity,
                'mrp' => $twelveCard[0]->mrp,
                'winning' => $twelveCard[0]->winning_price
            ];
            array_push($returnArray,$x);
        }

        if($game_id == 3){
            $sixteenCard = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$requestedData->combination_id." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 4 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'Sixteen Card',
                'quantity' => $sixteenCard[0]->quantity,
                'mrp' => $sixteenCard[0]->mrp,
                'winning' => $sixteenCard[0]->winning_price
            ];
            array_push($returnArray,$x);
        }

        if($game_id == 4){
            $singleIndividual = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$requestedData->combination_id." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 6 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'Sixteen Card',
                'quantity' => $singleIndividual[0]->quantity,
                'mrp' => $singleIndividual[0]->mrp,
                'winning' => $singleIndividual[0]->winning_price
            ];
            array_push($returnArray,$x);
        }

        if($game_id == 5){

            $doubleData = DoubleNumberCombination::find($requestedData->combination_id);

            $doubleIndividual = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$requestedData->combination_id." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 7 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'Double Individual',
                'quantity' => $doubleIndividual[0]->quantity,
                'mrp' => $doubleIndividual[0]->mrp,
                'winning' => $doubleIndividual[0]->winning_price
            ];
            array_push($returnArray,$x);


            $andarNumber = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$doubleData->andar_number_id." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 8 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'andar number',
                'quantity' => $andarNumber[0]->quantity,
                'mrp' => $andarNumber[0]->mrp,
                'winning' => $andarNumber[0]->winning_price
            ];
            array_push($returnArray,$x);

            $baharNumber = DB::select("select sum(quantity) as quantity, max(play_details.mrp) as mrp, max(game_types.winning_price) as winning_price from play_details
            inner join play_masters on play_masters.id = play_details.play_master_id
            inner join game_types on game_types.id = play_details.game_type_id
            where play_details.combination_number_id = ".$doubleData->bahar_number_id." and play_masters.game_id = ".$game_id." and date(play_masters.created_at) = ? and play_details.game_type_id = 9 and play_masters.draw_master_id = ".$activeDraw,[$today]);

            $x = [
                'game_type_name' => 'bahar number',
                'quantity' => $baharNumber[0]->quantity,
                'mrp' => $baharNumber[0]->mrp,
                'winning' => $baharNumber[0]->winning_price
            ];
            array_push($returnArray,$x);

        }


        return response()->json(['success'=>1,'data'=> $returnArray], 200,[],JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ManualResult  $manualResult
     * @return \Illuminate\Http\Response
     */
    public function show(ManualResult $manualResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ManualResult  $manualResult
     * @return \Illuminate\Http\Response
     */
    public function edit(ManualResult $manualResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ManualResult  $manualResult
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ManualResult $manualResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ManualResult  $manualResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManualResult $manualResult)
    {
        //
    }
}
