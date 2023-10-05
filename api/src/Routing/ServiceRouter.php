<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Routing;

class ServiceRouter extends Router
{
  public static function parseCsv(): void
  {
    self::execute(new \Pikil\CsvReader\Commands\ParseCsv());
  }

  public static function updateEmail(): void
  {
    self::execute(new \Pikil\CsvReader\Commands\UpdateEmail());
  }

  public static function getBySessionId(): void
  {
    self::execute(new \Pikil\CsvReader\Commands\GetBySession());
  }
}
