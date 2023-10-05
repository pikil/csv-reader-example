<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Commands;

use \Pikil\CsvReader\Utils\Result;

class Command
{
  public string $error;

  public function __construct(protected array $args = []) {}

  public function isValid(): bool
  {
    if (!$this->isRequestDataValid()) {
      $this->error = 'Invalid request...';
      return false;
    }

    return true;
  }

  public function isRequestDataValid(): bool
  {
    return true;
  }

  public function error(): array
  {
    if (is_null($this->error))
      $this->error = Result::ERROR;

    return Result::errorMessage($this->error);
  }

  public function execute(): array
  {
    return Result::errorMessage('Function is not implemented...');
  }
}
