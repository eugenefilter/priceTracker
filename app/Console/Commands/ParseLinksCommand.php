<?php

namespace App\Console\Commands;

use App\Services\LinkParsingService;
use Illuminate\Console\Command;

class ParseLinksCommand extends Command
{
  protected $signature = 'links:parse';
  protected $description = 'парсер ссылок';


  public function handle(LinkParsingService $service)
  {
    $this->info('Начало обработки ссылок...');
    $service->handle();
    $this->info('Готово');
  }
}
