<?php

namespace Database\Factories;

use App\Models\UserDepartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;
class BelongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users     = User::where('is_super_admin', 0)->pluck('id')->all();
        $userId    = $this->faker->randomElement($users);
        $boardIdExist  = UserDepartment::where('user_id', $userId)->pluck('board_id')->all();
        $boardIds  = UserDepartment::where('user_id', '!=', $userId)
        ->whereNotIn('board_id', $boardIdExist)->pluck('board_id')->all();
        // $organization = Organization::where('us')
        $organization = Department::pluck('organization_id')->all();
        if(!$boardIds) {
            return [
                'user_id'         => $userId,
                'board_id'        => $this->faker->uuid(),
                'user_disp_order' => $this->faker->numberBetween(1,99),
                'organization_id' => $this->faker->randomElement($organization)
            ];
        }

        return [
            'user_id'           => $userId,
            'board_id'          => $this->faker->randomElement($boardIds),
            'user_disp_order'   => $this->faker->numberBetween(1,99),
            'organization_id' => $this->faker->randomElement($organization)

        ];
    }
}
