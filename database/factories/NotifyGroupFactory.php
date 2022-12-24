<?php

namespace Database\Factories;

use App\Models\NotifyRecipientGroup;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotifyGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NotifyRecipientGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $organizations = Organization::orderBy('created_at', 'ASC')->get();
        $organization = $this->faker->randomElement($organizations);
        $organizationId = $organization->id;
        $organizationName = $organization->name;
        return [
            'organization_id' => $organizationId,
            'name' => $organizationName . '-通知先' . $this->faker->numberBetween(1,500),
            'disp_order' => $this->faker->numberBetween(1,99),
        ];
    }
}
