<?php

namespace App\Http\Controllers;

use App\Models\PayOutSlab;
use App\Http\Requests\StorePayOutSlabRequest;
use App\Http\Requests\UpdatePayOutSlabRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class PayOutSlabController extends Controller
{
    public function get_all_payout_slabs()
    {
        $data = PayOutSlab::get();
        return response()->json(['success'=>1,'data'=> $data], 200,[],JSON_NUMERIC_CHECK);
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
     * @param  \App\Http\Requests\StorePayOutSlabRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayOutSlabRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayOutSlab  $payOutSlab
     * @return \Illuminate\Http\Response
     */
    public function show(PayOutSlab $payOutSlab)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayOutSlab  $payOutSlab
     * @return \Illuminate\Http\Response
     */
    public function edit(PayOutSlab $payOutSlab)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayOutSlabRequest  $request
     * @param  \App\Models\PayOutSlab  $payOutSlab
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayOutSlabRequest $request, PayOutSlab $payOutSlab)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayOutSlab  $payOutSlab
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayOutSlab $payOutSlab)
    {
        //
    }
}
