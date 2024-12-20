<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Routing\Controllers\Middleware;

class EventController extends Controller
{
    use CanLoadRelationships;


    private array $allowedRelations = ['user', 'attendees', 'attendees.user'];

    // protecting route
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = $this->loadRelationships(Event::query(), $this->allowedRelations);

        return EventResource::collection($query->latest()->paginate(4));
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
            "user_id" => $request->user()->id
        ]);

        return new EventResource($this->loadRelationships($event, $this->allowedRelations));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {

        return new EventResource($this->loadRelationships($event, $this->allowedRelations));
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

        return new EventResource($this->loadRelationships($event, $this->allowedRelations));
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