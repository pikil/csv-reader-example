<?php

declare(strict_types=1);

namespace Pikil\CsvReader\DB;

use Exception;

class Employee extends SQLAdapter {
  protected const TABLE = 'sample_db.employees';

  public const ID = 'record_id';
  public const SESSION_ID = 'session_id';
  public const COMPANY_ID = 'company_id';
  public const NAME = 'name';
  public const EMAIL = 'email';
  public const SALARY = 'salary';

  /**
   * Saving records into the database. The number and order of columns should match the number of values
   * @param array $cols Array of columns to use for saving
   * @param array $values Array of values. Either flat array for single record or multiple records
   * @return int Latest saved id
   * @throws Exception 
   */
  public static function saveNewRecords(array $cols, array $values): int
  {
    return self::saveArray(
      $cols,
      $values
    );
  }

  public static function getRecordsBySession(string $id): array
  {
    $join = Company::leftJoin(self::COMPANY_ID);
    $where = self::where(
      self::equals(self::SESSION_ID)
    );

    return self::getRecords(
      $join . $where,
      [$id],
      [
        self::TABLE . '.*',
        Company::table() . '.' . Company::NAME . ' company_name'
      ]
    );
  }

  public static function updateEmail(string $session_id, int $record_id, string $email): int
  {
    return self::update(
      [self::EMAIL => $email],
      self::where(
        self::and([
          self::equals(self::ID),
          self::equals(self::SESSION_ID)
        ])
      ),
      [
        $record_id,
        $session_id
      ]
    );
  }
}
