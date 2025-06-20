<?php

namespace WebSocket;

use Random\RandomException;

trait MaskData
{
  /**
   * Размаскировывает данные, полученные от сокета.
   * Проверяет бит маскирования для определения, нужно ли размаскировать.
   *
   * @param string $payload
   *
   * @return string
   */
  private function unmaskData(string $payload): string
  {
    $offset = 0; // Смещение для первого байта фрейма
    $finAndOpcode = ord($payload[$offset]); // FIN + Opcode
    $maskAndLength = ord($payload[$offset + 1]); // MASK bit + Payload Length

    $hasMask = ($maskAndLength & 0x80) === 0x80; // Проверяем, установлен ли бит маскирования
    $length = $maskAndLength & 0x7F; // Получаем длину полезной нагрузки

    $offset += 2; // Переходим к следующей части заголовка

    if ($length === 126) {
      // Длина в двух байтах
      $length = (ord($payload[$offset]) << 8) | ord($payload[$offset + 1]);
      $offset += 2;
    } elseif ($length === 127) {
      // Длина в восьми байтах
      // PHP не поддерживает 64-битные числа так легко, как другие языки,
      // но для длины полезной нагрузки до 2^63-1 это должно работать.
      // Для HTTP-сообщений WebSocket редко бывают такие большие длины.
      $length = (ord($payload[$offset]) << 56) |
        (ord($payload[$offset + 1]) << 48) |
        (ord($payload[$offset + 2]) << 40) |
        (ord($payload[$offset + 3]) << 32) |
        (ord($payload[$offset + 4]) << 24) |
        (ord($payload[$offset + 5]) << 16) |
        (ord($payload[$offset + 6]) << 8) |
        ord($payload[$offset + 7]);
      $offset += 8;
    }

    $masks = '';
    if ($hasMask) {
      $masks = substr($payload, $offset, 4);
      $offset += 4;
    }

    $data = substr($payload, $offset); // Сами данные

    $text = '';
    if ($hasMask) {
      // Если данные замаскированы, применяем маску
      for ($i = 0; $i < $length; ++$i) {
        $text .= $data[$i] ^ $masks[$i % 4];
      }
    } else {
      // Если данные не замаскированы (обычно от сервера), возвращаем как есть
      $text = $data;
    }

    return $text;
  }

  /**
   * Маскирует данные для отправки клиенту или серверу.
   *
   * @param string $text     Сообщение для отправки.
   * @param bool   $isClient true, если это клиент отправляет данные (нужно маскировать), false для сервера (не маскировать).
   *
   * @return string Замаскированные (или незамаскированные, если сервер) данные.
   * @throws RandomException
   */
  private function maskData(string $text, bool $isClient = false): string
  {
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);
    $header = '';
    $masks = '';
    $maskedText = '';

    if ($isClient) {
      $masks = random_bytes(4);
    }

    if ($length <= 125) {
      $header = pack('C*', $b1, $length | ($isClient ? 0x80 : 0));
    } elseif ($length > 125 && $length < 65536) {
      $header = pack('C*', $b1, 126 | ($isClient ? 0x80 : 0), $length >> 8, $length & 0xFF);
    } else {
      $header = pack('C*', $b1, 127 | ($isClient ? 0x80 : 0), $length >> 56, ($length >> 48) & 0xFF, ($length >> 40) & 0xFF, ($length >> 32) & 0xFF, ($length >> 24) & 0xFF, ($length >> 16) & 0xFF, ($length >> 8) & 0xFF, $length & 0xFF);
    }

    if ($isClient) {
      for ($i = 0; $i < $length; ++$i) {
        $maskedText .= $text[$i] ^ $masks[$i % 4];
      }
      return $header . $masks . $maskedText;
    } else {
      return $header . $text;
    }
  }
}
