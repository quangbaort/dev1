<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;
use App\Models\Department;

class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $organization = Organization::orderBy('created_at', 'ASC')->first();
        $organizationId = $organization->id;
        $organizationName = $organization->name;
        $organizationNameKana = $organization->name_kana;

        $departments = Department::where('organization_id', $organizationId)->pluck('id')->all();
        if($departments) array_push($departments, null);
        $mainIdx = $this->faker->numberBetween(1,500);
        $subIdx = $this->faker->numberBetween(1,500);
        $idx = $mainIdx . '-' . $subIdx;
        return [
            'organization_id' => $organizationId,
            'parent_id' => $this->faker->randomElement($departments),
            'name' => $organizationName . ' - 組織' . $idx,
            'name_kana' => $organizationNameKana . ' - ソシキ' . $idx,
            'disp_order' => $this->faker->numberBetween(1,99),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}
