<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;

class HolidayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $organizations = Organization::pluck('id')->all();
        return [
            'organization_id' => $this->faker->randomElement($organizations),
            'name' => $this->faker->name(),
            'date' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 years')->format('Y/m/d'),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}
