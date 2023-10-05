<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Commands;

use \Pikil\CsvReader\DB\Employee;
use \Pikil\CsvReader\Traits\Modifiers;
use \Pikil\CsvReader\Utils\Result;
use \Throwable;

final class GetBySession extends Command
{
  use Modifiers;

  private string $session_id;

  public function __construct() {
    $this->session_id = (string) (self::sanitiseString($_GET['id'] ?? ''));
  }

  public function isValid(): bool
  {
    if (empty($this->session_id)) {
      $this->error = 'The data is malformed...';
      return false;
    }

    return true;
  }

  public function execute(): array
  {
    try {
      $records = Employee::getRecordsBySession($this->session_id);

      if (empty($records))
        return Result::failArray(['No records found for this session...']);

      return Result::successArray($records);
    } catch (Throwable $th) {
      return Result::notFound();
    }
  }
}
