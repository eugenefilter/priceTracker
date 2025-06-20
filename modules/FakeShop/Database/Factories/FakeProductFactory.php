<?php

namespace FakeShop\Database\Factories;

use FakeShop\Models\FakeProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FakeProductFactory extends Factory
{
  protected $model = FakeProduct::class;

  /**
   * Define the model's default state.
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $title = $this->faker->words(3, true);

    return [
      'title' => ucfirst($title),
      'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 999),
      'price' => $this->faker->randomFloat(2, 10, 1000),
      'available' => $this->faker->boolean(80),
    ];
  }
}
