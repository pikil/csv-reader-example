<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Commands;

use \Generator;
use \Pikil\CsvReader\DB\Company;
use \Pikil\CsvReader\DB\Employee;
use \Pikil\CsvReader\Traits\{
  Modifiers,
  Validation
};
use \Pikil\CsvReader\Utils\Result;
use \Throwable;

final class ParseCsv extends Command
{
  use Validation;
  use Modifiers;

  private const DELIMITER = ',';
  private const BATCH_LIMIT = 10000;

  private array $file;
  private static string $session_id; // This value sort of bypasses the User authentication mechanism for the simplicity's sake

  public function __construct() {
    $this->file = (array) ($_FILES['file'] ?? []);
    self::$session_id = self::randomString(15);
  }

  private static function randomString(int $length = 10): string
  {
    $chr = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $chrLen = strlen($chr);
      $rnd = '';
  
      for ($i = 0; $i < $length; $i++)
        $rnd .= $chr[rand(0, $chrLen - 1)];
  
      return $rnd;
  }

  private static function convertToBytes(string $size): int
  {
    $unit = strtoupper(substr($size, -1));
    $value = (int) substr($size, 0, -1);

    switch ($unit) {
        case 'P':
            $value *= 1024; // Petabyte
        case 'T':
            $value *= 1024; // Terabyte
        case 'G':
            $value *= 1024; // Gigabyte
        case 'M':
            $value *= 1024; // Megabyte
        case 'K':
            $value *= 1024; // Kilobyte
    }

    return $value;
  }

  public function isValid(): bool
  {
    if (empty($this->file)) {
      $this->error = 'Could not identify the file...';
      return false;
    }

    if ($this->file['error']) {
      $this->error = 'The file is malformed...';
      return false;
    }

    if (
      $this->file['size'] > self::convertToBytes(ini_get('post_max_size'))
      || $this->file['size'] > self::convertToBytes(ini_get('upload_max_filesize'))
    ) {
      $this->error = 'The file is too big...';
      return false;
    }

    return true;
  }

  private static function readCSV($path): Generator
  {
    if (($file = fopen($path, 'r')) !== false) {
      while (($data = fgetcsv($file, null, self::DELIMITER)) !== false)
        yield $data;

      fclose($file);
    }
  }

  private static function parseAndSave(array $file): array|string
  {
    $rows = self::readCSV($file['tmp_name']);
    $companies = [];

    foreach ($rows as $index => $row) {
      if (count($row) !== 4)
        return 'Incorrect number of elements on line ' . $index . '. Check that there is no empty line in the end.';

      // Ignoring headings
      if ($row[3] === 'Salary')
        continue;

      if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]))
        return 'Empty data on line ' . $index;

      if (!is_numeric($row[3]) || $row[3] <= 0)
        return 'Incorrect Salary value on line ' . $index;

      if ($index >= self::BATCH_LIMIT)
        return 'No more than ' . self::BATCH_LIMIT . ' is allowed to be saved at once...';

      $email = self::sanitiseString($row[2]);

      if (!self::isValidEmail($email))
        return 'Invalid email on line ' . $index;

      if (!isset($companies[$row[0]]))
        $companies[$row[0]] = [];

      $companies[$row[0]][] = [
        self::sanitiseString($row[1]),
        $email,
        (int) $row[3]
      ];
    }

    Employee::beginTransaction();

    $to_save = [];
    $db_companies = Company::saveNames(array_keys($companies));

    foreach ($db_companies as $db_company) {
      $employees = $companies[$db_company[Company::NAME]] ?? [];

      foreach ($employees as $e) {
        $to_save[] = [
          ...$e,
          self::$session_id,
          $db_company[Company::ID]
        ];
      }
    }

    Employee::saveNewRecords(
      [
        Employee::NAME,
        Employee::EMAIL,
        Employee::SALARY,
        Employee::SESSION_ID,
        Employee::COMPANY_ID
      ],
      $to_save
    );

    Employee::commitTransaction();

    // Fetching newly stored data from Database
    // Ideally, we can save on this DB round trip by using just saved data, as we have it anyway
    return Employee::getRecordsBySession(self::$session_id);
  }

  public function execute(): array
  {
    try {
      $parsed = self::parseAndSave($this->file);

      if (is_string($parsed))
        return Result::failArray([$parsed]);

      return Result::successArray($parsed);
    } catch (Throwable $th) {
      return Result::notFound();
    }
  }
}
