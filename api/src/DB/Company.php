<?php

declare(strict_types=1);

namespace Pikil\CsvReader\DB;

class Company extends SQLAdapter {
  protected const TABLE = 'sample_db.companies';

  public const ID = 'id';
  public const NAME = 'name';

  public  static function saveNames(array $names): array
  {
    self::saveArray(
      [self::NAME],
      array_map(
        function($n) { return [$n]; },
        $names
      ),
      ['upsert' => true]
    );

    return self::getRecords(
      self::where(self::in(self::NAME, $names)),
      $names,
      [self::ID, self::NAME]
    );
  }
}
