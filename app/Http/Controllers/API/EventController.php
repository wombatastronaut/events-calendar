<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display the events.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = [];
        $events = Event::all();

        foreach ($events as $event) {
            $start_date = Carbon::createFromDate($event->start);
            $end_date = Carbon::createFromDate($event->end);
            $date = $start_date;

            while ($date <= $end_date) {
                // Check if the date is within event days of week
                if (in_array($date->dayOfWeek, $event->frequency)) {
                    $result[] = [
                        'id' => $event->id,
                        'title' => $event->title,
                        'start' => $date->copy()
                            ->format('Y-m-d')
                    ];
                }

                $date->addDays(1);
            }
        }

        return response($result);
    }

    /**
     * Store a new event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = Event::create($request->all());

        return response([
            'success' => $result ? 1 : 0
        ]);
    }

    /**
     * Display the specified event.
     *
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return response($event);
    }

    /**
     * Update the specified event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $result = tap($event)->update($request->all());

        return response([
            'success' => $result ? 1 : 0
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response([], 204);
    }
}
