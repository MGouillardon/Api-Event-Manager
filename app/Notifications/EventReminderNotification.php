<?php

namespace App\Notifications;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Event $event
    ) {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $startAt = Carbon::parse($this->event->start_at)->format('l jS F Y \a\t g:i A');

        return (new MailMessage)
            ->line('Reminder: You have an event coming up soon!')
            ->action('You can see your event here', route('events.show', $this->event->id))
            ->line("The event {$this->event->name} starts at {$startAt}.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'event_start_at' => $this->event->start_at,
        ];
    }
}