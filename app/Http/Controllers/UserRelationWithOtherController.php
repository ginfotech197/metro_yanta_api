<?php

namespace App\Http\Controllers;

use App\Models\UserRelationWithOther;
use App\Http\Requests\StoreUserRelationWithOtherRequest;
use App\Http\Requests\UpdateUserRelationWithOtherRequest;

class UserRelationWithOtherController extends Controller
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
     * @param  \App\Http\Requests\StoreUserRelationWithOtherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRelationWithOtherRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserRelationWithOther  $userRelationWithOther
     * @return \Illuminate\Http\Response
     */
    public function show(UserRelationWithOther $userRelationWithOther)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserRelationWithOther  $userRelationWithOther
     * @return \Illuminate\Http\Response
     */
    public function edit(UserRelationWithOther $userRelationWithOther)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRelationWithOtherRequest  $request
     * @param  \App\Models\UserRelationWithOther  $userRelationWithOther
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRelationWithOtherRequest $request, UserRelationWithOther $userRelationWithOther)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserRelationWithOther  $userRelationWithOther
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserRelationWithOther $userRelationWithOther)
    {
        //
    }
}
