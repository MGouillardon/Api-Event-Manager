<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Notifications\EventReminderNotification;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send event reminders to attendees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::with('attendees.user')->whereBetween('start_at', [
            now(),
            now()->addDay()
        ])->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $this->info("Found {$eventCount} {$eventLabel} to remind attendees about");

        $events->each(
            fn($event) => $event->attendees->each(
                fn($attendee) => /** $this->info("Reminding {$attendee->user->name} about {$event->name}") */
                $attendee->user->notify(
                    new EventReminderNotification($event)
                )
            )
        );
    }
}