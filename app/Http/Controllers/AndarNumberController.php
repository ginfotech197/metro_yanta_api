<?php

namespace App\Http\Controllers;

use App\Http\Resources\AndarResource;
use App\Models\AndarNumber;
use App\Http\Requests\StoreAndarNumberRequest;
use App\Http\Requests\UpdateAndarNumberRequest;

class AndarNumberController extends Controller
{
    public function get_all_andar_number()
    {
        $data = AndarNumber::get();
        return response()->json(['success'=>1,'data'=>AndarResource::collection($data)], 200,[],JSON_NUMERIC_CHECK);
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
     * @param  \App\Http\Requests\StoreAndarNumberRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAndarNumberRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AndarNumber  $andarNumber
     * @return \Illuminate\Http\Response
     */
    public function show(AndarNumber $andarNumber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AndarNumber  $andarNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(AndarNumber $andarNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAndarNumberRequest  $request
     * @param  \App\Models\AndarNumber  $andarNumber
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAndarNumberRequest $request, AndarNumber $andarNumber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AndarNumber  $andarNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(AndarNumber $andarNumber)
    {
        //
    }
}
