<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::paginate(16);

        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => ["required", "string", "max:255"],
            "description" => ["nullable", "string"],
            "start_time" => ["required", "date"],
            "end_time" => ["required", "date", "after:start_date"],
        ]);

        $event = Event::create([

            "name" => $request->name,
            "description" => $request->description,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "user_id" => 1
        ]);

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
    public function update(Request $request, Event $event)
    {


        $event->update($request->validate([
            "name" => ["sometimes", "string", "max:255"],
            "description" => ["nullable", "string"],
            "start_time" => ["sometimes", "date"],
            "end_time" => ["sometimes", "date", "after:start_date"],
        ]));

        return $event;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        // return response()->json([
        //     'message' => "Event deleted successfully"
        // ]);

        return response([
            "message" => "event deleted"
        ], 200);
    }
}