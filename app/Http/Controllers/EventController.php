<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class EventController extends Controller
{
    public function calendar()
    {
        $user = auth()->user();
        
        // Selected date (defaults to today)
        $selectedDateStr = request('date', now()->toDateString());
        $selectedDate = \Carbon\Carbon::parse($selectedDateStr);
        
        // Calculate the week days for the center view
        // Using Sunday as start of the week
        $startOfWeek = $selectedDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfWeek = $selectedDate->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
        
        $weekDays = [];
        $current = $startOfWeek->copy();
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = $current->copy();
            $current->addDay();
        }

        // Fetch events grouped by Y-m-d format
        $events = Event::where('user_id', $user->id)
            ->orderBy('event_date')
            ->get()
            ->groupBy(function($event) {
                return $event->event_date->format('Y-m-d');
            });

        // Compute mini-calendar days for the month of the selected date
        $monthStr = request('month', $selectedDate->format('Y-m'));
        $month = \Carbon\Carbon::parse($monthStr . '-01');

        $startOfCalendar = $month->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfCalendar = $month->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);

        $days = [];
        $current = $startOfCalendar->copy();
        while($current <= $endOfCalendar) {
            $days[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === $month->month,
                'events' => $events->get($current->format('Y-m-d'), [])
            ];
            $current->addDay();
        }

        // Fetch active tasks for the sidebar
        $tasks = \App\Models\Task::where('user_id', $user->id)
            ->where('is_done', false)
            ->orderBy('id', 'desc')
            ->get();

        // Fetch tasks for the visible week range to display on the calendar grid
        $weekTasks = \App\Models\Task::where('user_id', $user->id)
            ->where(function($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('task_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                      ->orWhereBetween('carry_over_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
            })
            ->get()
            ->groupBy(function($task) {
                return $task->carry_over_date ?: $task->task_date;
            });
            
        // Fetch time blocks for task forms
        $blocks = \App\Models\TimeBlock::where('user_id', $user->id)
            ->orderBy('starts_at')
            ->get();

        // Fetch notes for the main sidebar scratchpad
        $notes = \App\Models\Note::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('calendar', compact('user', 'selectedDate', 'weekDays', 'days', 'month', 'events', 'tasks', 'weekTasks', 'blocks', 'notes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $event = Event::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'title' => $request->title,
                'event_date' => $request->event_date,
                'start_time' => $request->start_time ?: null,
                'end_time' => $request->end_time ?: null,
                'description' => $request->description,
                'type' => $request->type ?? 'event',
            ]
        );

        app(NotionSyncService::class)->syncEvent($event);

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        app(NotionSyncService::class)->deleteEntity($event);
        $event->delete();
        return response()->json(['message' => 'Event deleted']);
    }
}
