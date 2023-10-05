<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Traits;

trait Modifiers {
  private static function sanitiseSpaces(string $s = ''): string
  {
    return trim(preg_replace('/\s+/', ' ', $s));
  }

  private static function sanitiseString(string $s = ''): string
  {
    $s = trim($s);
    $s = stripslashes($s);
    $s = htmlspecialchars($s);
    $s = self::sanitiseSpaces($s);

    return $s;
  }
}
