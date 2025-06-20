<?php

namespace FakeShop\Database\Seeders;

use FakeShop\Database\Factories\FakeProductFactory;
use Illuminate\Database\Seeder;

class FakeProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    FakeProductFactory::new()->count(20)->create();
  }
}
