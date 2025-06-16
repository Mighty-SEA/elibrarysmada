<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $faker = $this->faker;
        $requestDate = $faker->dateTimeBetween('2025-06-01', '2025-06-28');
        $dueDate = (clone $requestDate)->modify('+7 days');
        return [
            'status' => 'dipinjam',
            'request_date' => $requestDate,
            'approval_date' => $requestDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'fine_amount' => 0,
            'notes' => null,
        ];
    }
} 