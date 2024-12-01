<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Event::query();
        $allowedRelations = ['user', 'attendees', 'attendees.user'];

        foreach ($allowedRelations as $relation) {
            $query->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $q->with($relation)
            );
        }

        $this->shouldIncludeRelation("user");
        return EventResource::collection($query->latest()->paginate(4));
    }

    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query("include");

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(",", $include));

        return in_array($relation, $relations);
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

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // return $event;

        $event->load('user', 'attendees');

        return new EventResource($event);
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

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response(status: 204);
    }
}