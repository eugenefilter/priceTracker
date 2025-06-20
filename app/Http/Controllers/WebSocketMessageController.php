<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Random\RandomException;
use WebSocket\WebSocketServer;

final class WebSocketMessageController extends Controller
{
  public function __construct(private readonly WebSocketServer $webSocketServer)
  {
  }

  /**
   * @throws RandomException
   */
  public function sendMessage(Request $request)
  {
    $request->validate([
      'user_id' => 'required|integer',
      'data' => 'required|array',
    ]);

    $userId = $request->input('user_id');
    $data = $request->input('data');

    $success = $this->webSocketServer->sendJsonToUser($userId, $data);

    if ($success) {
      return response()->json(['status' => 'success', 'message' => 'Сообщение успешно отправлено.']);
    } else {
      return response()->json(['status' => 'error', 'message' => 'Не удалось отправить сообщение. Возможно, пользователь не подключен.'], 404);
    }
  }
}
