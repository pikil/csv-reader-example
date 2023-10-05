<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Commands;

use \Pikil\CsvReader\DB\Employee;
use \Pikil\CsvReader\Traits\{
  Modifiers,
  Validation
};
use \Pikil\CsvReader\Utils\Result;
use \Throwable;

final class UpdateEmail extends Command
{
  use Validation;
  use Modifiers;

  private string $email;
  private string $session_id;
  private int $record_id;

  public function __construct() {
    $this->email = (string) (self::sanitiseString(($_POST['email'] ?? '')));
    $this->session_id = (string) (self::sanitiseString($_POST['sessionId'] ?? ''));
    $this->record_id = (int) ($_POST['id'] ?? 0);
  }

  public function isValid(): bool
  {
    if (empty($this->email) || !self::isValidEmail($this->email)) {
      $this->error = 'Cannot use this email...';
      return false;
    }

    if (empty($this->session_id) || $this->record_id <= 0) {
      $this->error = 'The data is malformed...';
      return false;
    }

    return true;
  }

  public function execute(): array
  {
    try {
      $updated = Employee::updateEmail($this->session_id, $this->record_id, $this->email);

      if (!$updated)
        return Result::failArray(['The record is not updated for this employee...']);

      return Result::successArray(['Updated!']);
    } catch (Throwable $th) {
      return Result::notFound();
    }
  }
}
