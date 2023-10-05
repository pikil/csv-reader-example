<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Utils;

use \Pikil\CsvReader\Utils\ResultResponse;

class Result
{
  public const SUCCESS = 'Success!';
  public const NOT_FOUND = 'Nothing is found...';
  public const FAILED = 'Request failed...';
  public const ERROR = 'Something went wrong...';
  public const UNAUTHORIZED = 'You are not signed in...';

  public static function unauthorized(): array
  {
    return (new ResultResponse(false, self::UNAUTHORIZED, []))->compose();
  }

  public static function successArray(array $arr = [], string $text = self::SUCCESS): array
  {
    return (new ResultResponse(true, $text, $arr))->compose();
  }

  public static function failArray(array $arr = [], string $text = self::FAILED): array
  {
    return (new ResultResponse(false, $text, $arr))->compose();
  }

  public static function errorMessage(string $text = self::FAILED): array
  {
    return (new ResultResponse(false, $text, []))->compose();
  }

  public static function notFound(): array
  {
    return (new ResultResponse(false, self::NOT_FOUND, []))->compose();
  }
}
