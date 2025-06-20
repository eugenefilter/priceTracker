<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

//class ProductUpdatedEvent implements ShouldBroadcast
class ProductUpdatedEvent implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   */
  public function __construct(
    public readonly Product $product,
  )
  {
    //
  }

  /**
   * Get the channels the event should broadcast on.
   * @return array<int, \Illuminate\Broadcasting\Channel>
   */
  public function broadcastOn(): array
  {
    return [
      new Channel('products'),
    ];
  }

  public function broadcastWith(): array
  {
    \Log::debug('Broadcasting ProductUpdatedEvent for product_id=' . $this->product->id);

    return [
      'id' => $this->product->id,
      'title' => $this->product->title,
      'price' => $this->product->price,
      'availability' => $this->product->availability,
      'user_id' => $this->product->user_id,
    ];
  }
}
