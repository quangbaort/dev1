<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $organizations = Organization::pluck('id')->all();
        $boards = Department::pluck('id')->all();
        $icons = ['mdi-airplane', 'mdi-car-hatchback', 'mdi-liquor', 'mdi-alert-circle',
            'mdi-pencil', 'mdi-emoticon-excited-outline', 'mdi-emoticon-dead-outline'];
        $admin = User::where('email', 'admin@sample.com')->pluck('id')->first();
        return [
            'organization_id'    => $this->faker->randomElement($organizations),
            'title'              => $this->faker->sentence(),
            'board_id'           => $this->faker->randomElement($boards),
            'is_allday'          => $this->faker->randomElement([0, 1]), //0：終日無効、1：終日有効
            'is_general_meeting' => $this->faker->randomElement([0, 1]), //0：総会でない、1：総会
            'start'              => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 weeks')->format('Y/m/d'),
            'end'                => $this->faker->dateTimeBetween($startDate = '1 weeks', $endDate = '2 weeks')->format('Y/m/d'),
            'place'              => $this->faker->paragraph($nbSentences = 2),
            'place_url'          => $this->faker->url(),
            'detail'             => $this->faker->paragraph($nbSentences = 2),
            'detail_url'         => $this->faker->url(),
            'period'             => $this->faker->dateTime($max = 'now', $timezone = null),
            'ok_title'           => $this->faker->realText($maxNbChars = 15, $indexSize = 1),
            'cancel_title'       => $this->faker->realText($maxNbChars = 15, $indexSize = 1),
            'theme_color'        => $this->faker->hexColor(),
            'icon'               => $this->faker->randomElement($icons),
            'repeat'             => $this->faker->randomElement([0, 1, 2, 3]), //0：無効、1：日、2：週、3：月
            'repeat_week'        => $this->faker->dayOfWeek($max = 'now'),
            'repeat_date'        => $this->faker->dayOfMonth($max = 'now'),
            'repeat_start'       => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 weeks')->format('Y/m/d'),
            'repeat_end'         => $this->faker->dateTimeInInterval($startDate = '1 weeks', $interval = '2 weeks'),
            'created_by'         => $admin,
        ];
    }
}
