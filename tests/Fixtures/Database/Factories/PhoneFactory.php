<?php

namespace ApiAutoPilot\ApiAutoPilot\Database\Factories;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{

    protected $model = Phone::class;

    public function definition()
    {
        return [
            'provider_logo' => $this->faker->imageUrl,
            'number' => $this->faker->phoneNumber,
        ];
    }
}
