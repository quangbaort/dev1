<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $mainIdx = $this->faker->numberBetween(1,500);
        $subIdx = $this->faker->numberBetween(1,500);
        $idx = $mainIdx . '-' . $subIdx;
        return [
            'name' => '契約 ' . $idx,
            'simple_name' => 'Simple Organization ' . $idx,
            'name_kana' => 'ケイヤク' . $idx,
            'zip_code' => $this->faker->randomNumber(7),
            'prefecture' => $this->faker->numberBetween(1,47),
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'building' => $this->faker->buildingNumber(),
            'tel' => $this->faker->randomNumber(5) . '' . $this->faker->randomNumber(6),
            'fax' => $this->faker->randomNumber(5) . '' . $this->faker->randomNumber(6),
            'sort' => $this->faker->numberBetween(1,99),
            'account_limit' => $this->faker->randomNumber(4),
            'storage_limit' => $this->faker->randomNumber(2),
            's3_bucket_name' => $this->faker->name(),
            'calendar_enabled' => $this->faker->boolean(),
            'news_enabled' => $this->faker->boolean(),
            'safety_check_enabled' => $this->faker->boolean(),
            'library_enabled' => $this->faker->boolean(),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}
