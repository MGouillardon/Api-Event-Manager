<?php

namespace Database\Seeders;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttendeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        User::all()->each(function ($user) use ($events) {
            $eventsToAttend = $events->random(random_int(1, 5));

            $eventsToAttend->each(function ($event) use ($user) {
                Attendee::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ]);
            });
        });
    }
}