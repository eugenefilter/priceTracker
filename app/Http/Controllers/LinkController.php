<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Link\CreateLinkAction;
use App\Http\Requests\AddUrlHttpRequest;
use App\Repository\LinkRepository;
use Illuminate\Validation\ValidationException;

final class LinkController extends Controller
{
  public function index(LinkRepository $repository)
  {
    $links = $repository->forUser(auth()->id());

    return inertia()->render('Links/Index', [
      'links' => $links,
    ]);
  }

  /**
   * @throws ValidationException
   */
  public function store(AddUrlHttpRequest $request, CreateLinkAction $action)
  {
    $result = $action->handle(
      url: $request->get('url'),
      userId: auth()->id()
    );

    if ($result) {
      return redirect()->route('links')->with('success', 'Ссылка добавлена');
    }

    return redirect()->with('Ошибка добавления ссылки');
  }
}
