<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\News;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $organizations = Organization::pluck('id')->all();
        $departments = Department::pluck('id')->all();
        $users = User::pluck('id')->all();
        $icons = ['mdi-airplane', 'mdi-car-hatchback', 'mdi-liquor', 'mdi-alert-circle',
            'mdi-pencil', 'mdi-emoticon-excited-outline', 'mdi-emoticon-dead-outline'];
        return [
            'organization_id' => $this->faker->randomElement($organizations),
            'board_id' => $this->faker->randomElement($departments),
            'title' => $this->faker->name(),
            'image_s3_url' => $this->faker->imageUrl($width = 640, $height = 480, $category = 'games', $randomize = true, $word = null),
            'start' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 years')->format('Y/m/d'),
            'end' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 years')->format('Y/m/d'),
            'place' => $this->faker->city(),
            'place_url' => $this->faker->url(),
            'detail' => $this->faker->paragraph(),
            'detail_url' => $this->faker->url(),
            'theme_color' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement($icons),
            'created_by' => $this->faker->randomElement($users),
        ];
    }
}
