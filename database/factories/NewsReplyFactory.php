<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsReplyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewsResponse::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $news = News::pluck('id')->all();
        $users = User::pluck('id')->all();
        return [
            'news_id' => $this->faker->randomElement($news),
            'user_id' => $this->faker->randomElement($users),
            'read_at' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 years')->format('Y/m/d'),
            'read' => $this->faker->numberBetween(0,1),
            // 'token' => $this->faker->token(),
            'created_by' => $this->faker->randomElement($users),
        ];
    }
}
