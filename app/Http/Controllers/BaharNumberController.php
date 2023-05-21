<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaharResource;
use App\Models\BaharNumber;
use App\Http\Requests\StoreBaharNumberRequest;
use App\Http\Requests\UpdateBaharNumberRequest;

class BaharNumberController extends Controller
{
    public function get_all_bahar_number()
    {
        $data = BaharNumber::get();
        return response()->json(['success'=>1,'data'=>BaharResource::collection($data)], 200,[],JSON_NUMERIC_CHECK);
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
     * @param  \App\Http\Requests\StoreBaharNumberRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBaharNumberRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BaharNumber  $baharNumber
     * @return \Illuminate\Http\Response
     */
    public function show(BaharNumber $baharNumber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BaharNumber  $baharNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(BaharNumber $baharNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBaharNumberRequest  $request
     * @param  \App\Models\BaharNumber  $baharNumber
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBaharNumberRequest $request, BaharNumber $baharNumber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BaharNumber  $baharNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(BaharNumber $baharNumber)
    {
        //
    }
}
