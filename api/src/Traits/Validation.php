<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Traits;

trait Validation {
  private static function isValidEmail(string $email): string|bool
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }
}
