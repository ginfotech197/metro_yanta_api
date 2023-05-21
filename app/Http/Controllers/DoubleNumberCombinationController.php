<?php

namespace App\Http\Controllers;

use App\Models\DoubleNumberCombination;
use App\Http\Requests\StoreDoubleNumberCombinationRequest;
use App\Http\Requests\UpdateDoubleNumberCombinationRequest;
use App\Http\Resources\DoubleNumberCombinationResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Double;

class DoubleNumberCombinationController extends Controller
{
    public function get_all_double_number()
    {
        $double = Cache::remember('get_all_double_number', 3000000, function () {
            return DB::select("select id, single_number_id, double_number, visible_double_number, andar_number_id, bahar_number_id from double_number_combinations");
        });

        // return response()->json(['success'=>1,'data'=> $double], 200);
        return response()->json(['success'=>1,'data'=> DoubleNumberCombinationResource::collection($double)], 200);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDoubleNumberCombinationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDoubleNumberCombinationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DoubleNumberCombination  $doubleNumberCombination
     * @return \Illuminate\Http\Response
     */
    public function show(DoubleNumberCombination $doubleNumberCombination)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DoubleNumberCombination  $doubleNumberCombination
     * @return \Illuminate\Http\Response
     */
    public function edit(DoubleNumberCombination $doubleNumberCombination)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDoubleNumberCombinationRequest  $request
     * @param  \App\Models\DoubleNumberCombination  $doubleNumberCombination
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDoubleNumberCombinationRequest $request, DoubleNumberCombination $doubleNumberCombination)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DoubleNumberCombination  $doubleNumberCombination
     * @return \Illuminate\Http\Response
     */
    public function destroy(DoubleNumberCombination $doubleNumberCombination)
    {
        //
    }
}
