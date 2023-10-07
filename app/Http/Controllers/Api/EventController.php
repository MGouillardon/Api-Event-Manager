<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Event\StoreEventRequest;
use App\Http\Requests\Api\Event\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = [
        'user',
        'attendees',
        'attendees.user',
    ];

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('throttle:60,1')->only(['store', 'update', 'destroy']);
        // $this->middleware('throttle:api')->only(['store', 'update', 'destroy']); => can change it in app/Providers/RouteServiceProvider.php
        $this->authorizeResource(Event::class, 'event');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = $this->loadRelationships(Event::query());

        return EventResource::collection(
            $query->latest()->paginate()
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request, Event $event)
    {
        $event = Event::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        // Authorization can be done this way:
        // if(Gate::denies('update-event', $event)) {
        //     abort(403, 'You do not have permission to update this event.');
        // }
        // Or this way:
        // $this->authorize('update-event', $event);
        
        $event->update($request->validated());

        return new EventResource($this->loadRelationships($event));
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