<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Routing;

use \Pikil\CsvReader\{
  Utils\Result,
  Commands\Command
};

class Router
{
  private const AllOWED_METHODS = ['POST', 'GET']; // Methods allowed to be received by the API

  protected static $response = [
    'status' => false,
    'text' => Result::ERROR,
    'data' => []
  ];

  private static function _slugify(string $s = '', bool $to_lower_case = true, string $fill_with = ''): string
  {
    $s = preg_replace('/[^a-zA-Z0-9.\-_\s]/', $fill_with, $s);

    if ($to_lower_case)
      $s = strtolower($s);

    return trim(preg_replace('/\s+/', '-', $s), '-');
  }

  public function handleRequest(): void
  {
    if (empty($_SERVER['REQUEST_METHOD']) || !in_array($_SERVER['REQUEST_METHOD'], self::AllOWED_METHODS))
      self::routeError();

    $this->parseUrlCommand();
  }

  private static function buildClassName(string $name): string
  {
    $name = str_contains($name, '-')
      ? array_reduce(
          explode('-', $name),
          function ($accum, $n) { return $accum . ucfirst($n); },
          ''
        )
      : ucfirst($name);

    return __NAMESPACE__ . '\\' . $name . 'Router';
  }

  private static function buildMethodName(string $name): string
  {
    $method = self::_slugify($name);

    $method = (str_contains($method, '-'))
      ? array_reduce(explode('-', $method), function ($a, $b) {
          return $a . ucfirst($b);
        }, '')
      : ucfirst($method);

    return $method;
  }

  // Should be called before self::_GET() ever called because of self::$query_id unsetting
  private function parseUrlCommand(): void
  {
    $command_arr = explode('/', $_SERVER[getenv(('URI_ID'))] ?? '');

    if (count($command_arr) !== 5) {
      self::routeError();
      return;
    }

    $class_name = self::buildClassName($command_arr[3]);

    if (!class_exists($class_name)) {
      self::routeError();
      return;
    }

    $method = self::buildMethodName($command_arr[4]);

    if (!method_exists($class_name, $method)) {
      self::routeError();
      return;
    }

    $args = [];

    call_user_func_array([$class_name, $method], $args);
  }

  private static function routeError(): void
  {
    self::exitViaJson(Result::errorMessage('Unknown request...'));
  }

  protected static function exitViaJson(array $data = []): void
  {
    header('Content-Type: application/json');
    die(json_encode($data));
  }

  protected static function execute(Command $command): void
  {
    if (!$command->isValid())
      self::exitViaJson(Result::errorMessage($command->error));

    self::exitViaJson($command->execute());
  }
}
