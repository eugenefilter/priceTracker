<?php declare(strict_types=1);

namespace FakeShop\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class LoopFakeProductUpdater extends Command
{
    protected $signature = 'fake-shop:loop-prices {--interval=60}';
    protected $description = 'Запускает вечный цикл обновления фейковых цен';

    public function handle(): void
    {
        $interval = (int)$this->option('interval');
        if ($interval < 60) {
            $this->warn('Интервал не может быть меньше 60 секунд');
            return;
        }

        $this->info('Цикл обновления цен запущен...');

        while (true) {
            Artisan::call('fake-shop:update-prices');
            sleep($interval);
        }
    }
}
