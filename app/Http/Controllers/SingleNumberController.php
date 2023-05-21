<?php

namespace App\Http\Controllers;

use App\Models\SingleNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Resources\SingleNumbers;

class SingleNumberController extends Controller
{
    public function index()
    {
        $result =  SingleNumber::orderBy('single_order')->get();
        return response()->json(['success'=>1,'data'=>SingleNumbers::collection($result)], 200,[],JSON_NUMERIC_CHECK);
    }

    public function get_all_single_number()
    {
        $result = Cache::remember('get_all_single_number', 3000000, function () {
            return SingleNumber::select('id','single_name','single_number')->get();
        });
        return response()->json(['success'=>1,'data'=>SingleNumbers::collection($result)], 200);

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
     * @param  \App\Models\SingleNumber  $singleNumber
     * @return \Illuminate\Http\Response
     */
    public function show(SingleNumber $singleNumber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SingleNumber  $singleNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(SingleNumber $singleNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SingleNumber  $singleNumber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SingleNumber $singleNumber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SingleNumber  $singleNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(SingleNumber $singleNumber)
    {
        //
    }
}
