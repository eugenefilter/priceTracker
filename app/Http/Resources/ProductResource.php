<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   * @return array<string, mixed>
   */
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'url' => $this->url,
      'title' => $this->title,
      'price' => $this->price,
      'availability' => (bool)$this->availability,
      'created_at' => $this->created_at->toISOString(),
      'updated_at' => $this->updated_at->toISOString(),
      'histories' => ProductHistoryResource::collection(
        $this->whenLoaded('histories')
      ),
    ];
  }
}
