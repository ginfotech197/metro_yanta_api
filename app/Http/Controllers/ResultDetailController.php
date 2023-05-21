<?php

namespace App\Http\Controllers;

use App\Models\ResultDetail;
use App\Http\Requests\StoreResultDetailRequest;
use App\Http\Requests\UpdateResultDetailRequest;

class ResultDetailController extends Controller
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
     * @param  \App\Http\Requests\StoreResultDetailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResultDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ResultDetail  $resultDetail
     * @return \Illuminate\Http\Response
     */
    public function show(ResultDetail $resultDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ResultDetail  $resultDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(ResultDetail $resultDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResultDetailRequest  $request
     * @param  \App\Models\ResultDetail  $resultDetail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResultDetailRequest $request, ResultDetail $resultDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ResultDetail  $resultDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResultDetail $resultDetail)
    {
        //
    }
}
