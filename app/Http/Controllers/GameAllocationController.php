<?php

namespace App\Http\Controllers;

use App\Models\GameAllocation;
use App\Http\Requests\StoreGameAllocationRequest;
use App\Http\Requests\UpdateGameAllocationRequest;

class GameAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreGameAllocationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGameAllocationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GameAllocation  $gameAllocation
     * @return \Illuminate\Http\Response
     */
    public function show(GameAllocation $gameAllocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GameAllocation  $gameAllocation
     * @return \Illuminate\Http\Response
     */
    public function edit(GameAllocation $gameAllocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateGameAllocationRequest  $request
     * @param  \App\Models\GameAllocation  $gameAllocation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGameAllocationRequest $request, GameAllocation $gameAllocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GameAllocation  $gameAllocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(GameAllocation $gameAllocation)
    {
        //
    }
}
