<?php declare(strict_types=1);

namespace FakeShop\Controller;

use App\Http\Controllers\Controller;
use FakeShop\Repositories\FakeProductRepository;
use FakeShop\Request\PaginationHttpRequest;
use FakeShop\Request\PaginationRequest;

final class FakeShopController extends Controller
{
  public function index(
    PaginationHttpRequest $request,
    FakeProductRepository $repository
  )
  {
    $pagination = PaginationRequest::fromRequest($request);
    // Загружаем товары
    $products = $repository->getAl($pagination);

    return inertia()->render('FakeShop/Showcase', [
      'products' => $products,
      'filters' => [
        'page' => $pagination->page,
        'perPage' => $pagination->perPage,
        'sortBy' => $pagination->sortBy,
        'sortDir' => $pagination->sortDir,
      ],
    ]);
  }
}
