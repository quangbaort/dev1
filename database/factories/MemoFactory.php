<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;

class MemoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $organizations = Organization::pluck('id')->all();
        $icons = ['mdi-airplane', 'mdi-car-hatchback', 'mdi-liquor', 'mdi-alert-circle',
            'mdi-pencil', 'mdi-emoticon-excited-outline', 'mdi-emoticon-dead-outline'];
        return [
            'organization_id' => $this->faker->randomElement($organizations),
            'start' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 years')->format('Y/m/d'),
            'end' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 years')->format('Y/m/d'),
            'title' => $this->faker->sentence(),
            'detail' => $this->faker->paragraph(),
            'theme_color' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement($icons),
            'image_s3_url' => $this->faker->imageUrl($width = 640, $height = 480, $category = 'games', $randomize = true, $word = null),
            'url' => $this->faker->url(),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}
