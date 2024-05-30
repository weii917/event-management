<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Event::all();
    }

    /**  
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        // 驗證請求數據
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        // 合併驗證數據和其他數據
        $data = array_merge($validatedData, [
            'user_id' => 1,
        ]);

        // 使用合併後的數據創建事件
        $event = Event::create($data);
        return $event;
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return $event;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
