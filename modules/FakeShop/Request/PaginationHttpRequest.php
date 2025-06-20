<?php declare(strict_types=1);

namespace FakeShop\Request;

use Illuminate\Foundation\Http\FormRequest;

final class PaginationHttpRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'page' => 'integer|min:1',
      'perPage' => 'integer|min:1|max:100',
      'sortBy' => 'nullable|string',
      'sortDir' => 'nullable|string|in:asc,desc',
    ];
  }

}
