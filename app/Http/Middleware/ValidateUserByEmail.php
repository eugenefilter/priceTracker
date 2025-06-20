<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ValidateUserByEmail
{
  /**
   * Handle an incoming request.
   *
   * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
   */
  public function handle(Request $request, Closure $next)
  {
    $email = $request->query('email', $request->input('email'));

    if (!$email) {
      return response()->json(['error' => 'Email is required'], 400);
    }

    $user = User::query()->where('email', $email)->first();

    if (!$user) {
      return response()->json(['error' => 'Access Denied'], 403);
    }

    return $next($request);
  }
}
