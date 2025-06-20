<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductHistoryResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'product_id' => $this->product_id,
      'old_price' => (float)$this->old_price,
      'new_price' => (float)$this->new_price,
      'availability' => (bool)$this->availability,
      'changed_at' => $this->changed_at->toISOString(),
    ];
  }
}
