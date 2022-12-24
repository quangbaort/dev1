<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;

class CompanyFactory extends Factory
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

        $mainIdx = $this->faker->numberBetween(1,500);
        $subIdx = $this->faker->numberBetween(1,500);
        $idx = $mainIdx . '-' . $subIdx;
        return [
            'organization_id' => $organizationId,
            'name' => $organizationName . ' - 会社名' . $idx,
            'name_kana' => $organizationNameKana . 'カイシャメイ' . $idx,
            'zip_code' => $this->faker->randomNumber(7),
            'prefecture' => $this->faker->numberBetween(1,47),
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'building' => $this->faker->buildingNumber(),
            'tel' => $this->faker->randomNumber(5) . '' . $this->faker->randomNumber(6),
            'fax' => $this->faker->randomNumber(5) . '' . $this->faker->randomNumber(6),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}
