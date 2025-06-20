<?php

namespace App\Observers;

use App\Events\ProductUpdatedEvent;
use App\Models\Product;

class ProductObserver
{
  /**
   * Handle the Product "updated" event.
   */
  public function updated(Product $product): void
  {
    event(new ProductUpdatedEvent($product));
  }

}
