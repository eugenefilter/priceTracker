<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\ProductsChangeRepository;

class ProductsChangeController extends Controller
{
  public function index(ProductsChangeRepository $repository)
  {
    $products = $repository->forUser(auth()->id());

    return inertia()->render('Products/Change', [
      'products' => $products,
    ]);
  }
}
