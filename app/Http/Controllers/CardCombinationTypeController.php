<?php

namespace App\Http\Controllers;

use App\Models\CardCombinationType;
use App\Http\Requests\StoreCardCombinationTypeRequest;
use App\Http\Requests\UpdateCardCombinationTypeRequest;

class CardCombinationTypeController extends Controller
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
     * @param  \App\Http\Requests\StoreCardCombinationTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCardCombinationTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CardCombinationType  $cardCombinationType
     * @return \Illuminate\Http\Response
     */
    public function show(CardCombinationType $cardCombinationType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CardCombinationType  $cardCombinationType
     * @return \Illuminate\Http\Response
     */
    public function edit(CardCombinationType $cardCombinationType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCardCombinationTypeRequest  $request
     * @param  \App\Models\CardCombinationType  $cardCombinationType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCardCombinationTypeRequest $request, CardCombinationType $cardCombinationType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CardCombinationType  $cardCombinationType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CardCombinationType $cardCombinationType)
    {
        //
    }
}
