<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventNotifiesReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $events = Event::pluck('id')->all();
        $users = User::where('is_super_admin', 0)->pluck('id');
        return [
            'event_id'       => $this->faker->randomElement($events),
            'user_id'        => $this->faker->randomElement($users),
            'notified_at'    => $this->faker->dateTime($max = 'now', $timezone = null),
            'answered_at'    => $this->faker->dateTime($max = 'now'),
            'answer'         => $this->faker->randomElement([0, 1, 2, 3]), //0：未回答、1：出席（OK）、2：欠席（Cancel）、3：保留（Pending)
        ];
    }
}
