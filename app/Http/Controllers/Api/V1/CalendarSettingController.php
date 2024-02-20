<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\calendarSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorecalendarSettingRequest;
use App\Http\Requests\UpdatecalendarSettingRequest;

class CalendarSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'success';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorecalendarSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(calendarSetting $calendarSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(calendarSetting $calendarSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecalendarSettingRequest $request, calendarSetting $calendarSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(calendarSetting $calendarSetting)
    {
        //
    }
}
