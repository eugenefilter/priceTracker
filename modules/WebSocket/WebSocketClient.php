<?php declare(strict_types=1);

namespace WebSocket;

class WebSocketClient
{
  use MaskData;

  private $socket;

  public function __construct(
    private readonly string $host,
    private readonly int    $port
  )
  {
  }

  /**
   * Подключается к WebSocket серверу и выполняет хендшейк.
   * @return bool
   */
  public function connect(): bool
  {
    $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($this->socket === false) {
      echo "Error creating socket: " . socket_strerror(socket_last_error()) . "\n";
      return false;
    }

    if (@socket_connect($this->socket, $this->host, $this->port) === false) {
      echo "Error connecting to {$this->host}:{$this->port}: " . socket_strerror(socket_last_error($this->socket)) . "\n";
      return false;
    }

    $key = base64_encode(uniqid());
    $handshake = "GET / HTTP/1.1\r\n" .
      "Host: {$this->host}:{$this->port}\r\n" .
      "Upgrade: websocket\r\n" .
      "Connection: Upgrade\r\n" .
      "Sec-WebSocket-Key: {$key}\r\n" .
      "Sec-WebSocket-Version: 13\r\n\r\n";

    socket_write($this->socket, $handshake, strlen($handshake));
    $response = socket_read($this->socket, 2048);

    if (!str_contains($response, "101 Switching Protocols")) {
      echo "Handshake failed: " . $response . "\n";
      socket_close($this->socket);
      return false;
    }

    echo "Connected and Handshake successful.\n";
    return true;
  }

  /**
   * Отправляет данные на сервер после маскирования.
   *
   * @param string $message
   */
  public function send(string $message): void
  {
    $maskedMessage = $this->maskData($message, true);
    socket_write($this->socket, $maskedMessage, strlen($maskedMessage));
  }

  /**
   * Читает и размаскировывает данные с сервера.
   * @return string|false
   */
  public function receive(): string|false
  {
    $buffer = '';
    $bytes = @socket_recv($this->socket, $buffer, 2048, 0);
    if ($bytes === 0 || $bytes === false) {
      return false;
    }

    return $this->unmaskData($buffer);
  }

  /**
   * Закрывает соединение.
   */
  public function close(): void
  {
    if ($this->socket) {
      socket_close($this->socket);
      echo "Connection closed.\n";
    }
  }
}
