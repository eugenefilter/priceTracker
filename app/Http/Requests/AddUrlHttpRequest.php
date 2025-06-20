<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AddUrlHttpRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'url' => ['required', 'url', 'max:2048'],
    ];
  }

  public function messages(): array
  {
    return [
      'url.required' => 'Поле URL обязательно для заполнения.',
      'url.url' => 'Введите корректный URL.',
      'url.max' => 'URL не должен превышать 2048 символов.',
    ];
  }
}
