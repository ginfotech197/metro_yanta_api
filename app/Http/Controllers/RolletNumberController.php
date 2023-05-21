<?php

namespace App\Http\Controllers;

use App\Http\Resources\RolletNumberResource;
use App\Http\Resources\SingleNumbers;
use App\Models\RolletNumber;
use App\Http\Requests\StoreRolletNumberRequest;
use App\Http\Requests\UpdateRolletNumberRequest;
use App\Models\SingleNumber;
use Illuminate\Support\Facades\Cache;

class RolletNumberController extends Controller
{
    public function get_rollet_numbers()
    {
        $result = Cache::remember('get_all_rollet_numbers', 3000000, function () {
            return RolletNumber::select('id','rollet_number')->get();
        });
        return response()->json(['success'=>1,'data'=>RolletNumberResource::collection($result)], 200);
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
     * @param  \App\Http\Requests\StoreRolletNumberRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRolletNumberRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolletNumber  $rolletNumber
     * @return \Illuminate\Http\Response
     */
    public function show(RolletNumber $rolletNumber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RolletNumber  $rolletNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(RolletNumber $rolletNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRolletNumberRequest  $request
     * @param  \App\Models\RolletNumber  $rolletNumber
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRolletNumberRequest $request, RolletNumber $rolletNumber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolletNumber  $rolletNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(RolletNumber $rolletNumber)
    {
        //
    }
}
