<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UseCaseAction;
use App\Models\Product;
use App\Repository\ProductsRepository;

final class ProductsController extends Controller
{
  public function index(ProductsRepository $repository)
  {
    $products = $repository->forUser(auth()->id());

    return inertia()->render('Products/Index', [
      'products' => $products,
    ]);
  }

  public function check(Product $product, UseCaseAction $action): void
  {
    $action->handle(
      auth()->id(),
      $product->url
    );
  }
}
