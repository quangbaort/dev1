<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRefBoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $boards = Department::pluck('id')->all();
        $events = Event::pluck('id')->all();
        return [
            'event_id'  => $this->faker->randomElement($events),
            'board_id'  => $this->faker->randomElement($boards)
        ];
    }
}
