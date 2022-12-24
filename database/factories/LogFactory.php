<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class LogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::where('is_super_admin', false)->pluck('id')->all();

        $functions = ['S103', 'S200', 'S201', 'S300', 'S301', 'S400', 'S401', 'S500', 'S900', 'S910', 'S911', 'S920', 
        'S921', 'S930', 'S931', 'S940', 'S941', 'S950', 'S951', 'S960', 'S961', 'S970', 'S971'];
        return [
            'user_id' => $this->faker->randomElement($users),
            'date' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '1 years')->format('Y/m/d'),
            'organization_name' => 'Organization ' . $this->faker->numberBetween(1,500),
            'user_name' => 'User ' . $this->faker->numberBetween(1,500),
            'type' => $this->faker->randomElement([1, 2, 3]), //'1:追加、2:更新、3：削除'
            'function' => $this->faker->randomElement($functions),  //操作画面
            'detail' => $this->faker->paragraph(),
        ];
    }
}
