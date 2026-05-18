<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name'         => $this->faker->name(),
            'email'        => $this->faker->unique()->safeEmail(),
            'phone'        => $this->faker->numerify('11#########'),
            'score'        => 0,
            'status'       => 'pending',
            'processed_at' => null,
        ];
    }
}
