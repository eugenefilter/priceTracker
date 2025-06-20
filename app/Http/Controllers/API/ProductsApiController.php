<?php declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\User;
use App\Repository\ProductsRepository;
use Illuminate\Http\Request;

class ProductsApiController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request, ProductsRepository $repository)
  {
    $perPage = (int)$request->query('per_page', 10);
    $email = $request->query('email');

    $user = User::query()->where('email', $email)->first();
    if (!$user) {
      return response()->json(['error' => 'User not found'], 404);
    }

    $products = $repository->forUserWithHistory($user->id, $perPage);
    return ProductResource::collection($products);
  }
}
