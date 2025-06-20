export interface WSMessage {
  event: string;
  channel?: string;
  data: any;
}

type Listener = (data: any, msg: WSMessage) => void;

export class WebSocketService {
  private socket: WebSocket | null = null;
  private listeners: Record<string, Listener[]> = {};

  /**
   * @param host — хост WebSocket-сервера (без протокола)
   * @param port — порт WebSocket-сервера
   * @param userId — ваш идентификатор пользователя (для аутентификации на сервере)
   * @param channels — на какие каналы сразу подписаться
   */
  constructor(
    private host: string,
    private port: number,
    private userId: number,
    private channels: string[],
  ) {}

  /** Открывает соединение, шлёт user_id + подписки на каналы */
  connect(): void {
    if (this.socket) return;

    const url = `ws://${this.host}:${this.port}`;
    this.socket = new WebSocket(url);

    this.socket.onopen = () => {
      console.log('[WS] connected to', url);
      // аутентификация
      this.send({ user_id: this.userId });
      // подписываемся на каналы
      for (const chan of this.channels) {
        this.send({
          event: 'pusher:subscribe',
          data: { channel: chan },
        });
      }
    };

    this.socket.onmessage = (e) => {
      let msg: WSMessage;
      try {
        msg = JSON.parse(e.data);
      } catch {
        console.warn('[WS] invalid JSON:', e.data);
        return;
      }
      // вызываем всех слушателей для msg.event
      const ev = msg.event;
      if (ev && this.listeners[ev]) {
        for (const fn of this.listeners[ev]) {
          fn(msg.data, msg);
        }
      }
      // и wildcard-слушатели
      if (this.listeners['*']) {
        for (const fn of this.listeners['*']) {
          fn(msg.data, msg);
        }
      }
    };

    this.socket.onerror = (err) => {
      console.error('[WS] error', err);
    };
    this.socket.onclose = () => {
      console.log('[WS] closed');
      this.socket = null;
    };
  }

  /** Отправить в сокет любой объект */
  send(payload: any): void {
    if (this.socket && this.socket.readyState === WebSocket.OPEN) {
      this.socket.send(JSON.stringify(payload));
    } else {
      console.warn('[WS] socket not open');
    }
  }

  /**
   * Подписаться на события
   * @param event — имя события (msg.event)
   * @param callback — fn(data, rawMessage)
   */
  on(event: string, callback: Listener): void {
    if (!this.listeners[event]) {
      this.listeners[event] = [];
    }
    this.listeners[event].push(callback);

    this.send({
      event: 'pusher:subscribe',
      data: { channel: callback },
    });
  }

  /** Закрыть соединение */
  close(): void {
    this.socket?.close();
  }
}
