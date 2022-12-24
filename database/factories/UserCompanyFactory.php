<?php

namespace Database\Factories;

use App\Models\UserCompany;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $orgIds = Organization::orderBy('created_at', 'ASC')->pluck('id')->all();
        $users  = User::where('is_super_admin', 0)->pluck('id')->all();

        $userId = $this->faker->randomElement($users);
        $orgId  = $this->faker->randomElement($orgIds);

        $companyIdExist  = UserCompany::where('user_id', $userId)->pluck('company_id')->all();
        $companyIds = UserCompany::where('user_id', '!=', $userId)
            ->whereNotIn('company_id', $companyIdExist)->orderBy('created_at', 'ASC')->pluck('company_id')->all();

        $data = [
            'user_id'         => $userId,
            'organization_id' => $orgId,
            'company_id'      => $this->faker->uuid()
        ];
        if($companyIds) {
            $data['company_id'] = $this->faker->randomElement($companyIds);
        }
        return $data;
    }
}
