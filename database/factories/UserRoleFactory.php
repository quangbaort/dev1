<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::where('is_super_admin', 0)->pluck('id')->all();
        $orgId = Organization::orderBy('created_at', 'ASC')->pluck('id')->all();
        return [
            'user_id'           => $this->faker->randomElement($users),
            'organization_id'   => $this->faker->randomElement($orgId),
            'role'              => $this->faker->randomElement([ADMIN_ROLE, USER_ROLE])
        ];
    }
}
