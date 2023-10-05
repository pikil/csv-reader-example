<?php

declare(strict_types=1);

namespace Pikil\CsvReader\DB;

use \PDO;
use \Throwable;

abstract class SQLAdapter
{
  protected const ID = 'no_id';
  protected const TABLE = 'no_table';
  private static $pdo;

  private static function init(): void
  {
    try {
      if (!self::$pdo)
        self::$pdo = new PDO(
          'mysql:host=' . getenv('DB_HOST') . ';charset=utf8;',
          getenv('DB_USER'),
          getenv('DB_PWD'),
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_STRINGIFY_FETCHES => false
          ]
        );
    } catch (Throwable $th) {
      die('Cannot establish a DB connection...');
    }
  }

  public static function id(): string
  {
    return static::ID;
  }

  public static function table(): string
  {
    return static::TABLE;
  }

  private static function inTransaction(): bool
  {
    return self::$pdo->inTransaction();
  }

  public static function beginTransaction(): void
  {
    self::init();
    self::$pdo->beginTransaction();
  }

  public static function commitTransaction(): void
  {
    if (self::inTransaction())
      self::$pdo->commit();
  }

  public static function rollBackTransaction(): void
  {
    if (self::$pdo->inTransaction())
      self::$pdo->rollBack();
  }

  private static function execute_query(string $q, array $params, $show_last_id = true): array
  {
    $query = self::$pdo->prepare($q);
    $query->execute($params);

    if ($query->errorCode() !== '00000') {
      $msg = 'Query failed, error: ' . print_r(array_merge(
        $query->errorInfo(),
        ['query' => $q . ',', 'params' => $params]
      ), true);

      error_log($msg);
      die(print_r([$msg, debug_print_backtrace()], true));
    }

    return [
      'status' => true,
      'query' => $query,
      'id' => $show_last_id ? self::$pdo->lastInsertId() : 0
    ];
  }

  private static function dbSelect(string $table = 'no', string $cols = '*', string $addit = '', array $params = []): array
  {
    self::init();

    $result = self::execute_query(
      'SELECT ' . $cols . ' FROM ' . $table . ' ' . $addit,
      $params
    );
    return $result['query']->fetchAll();
  }

  private static function dbInsert(string $table = 'no', array $cols = [], array $params = [], array $options = []): int
  {
    self::init();

    if (empty($cols))
      return 0;

    $q = 'INSERT';

    if (isset($options['upsert']) && $options['upsert'] === true)
      $q .= ' IGNORE';

    $q .= ' INTO ' . $table;
    $values = '';
    $arr = [];

    foreach ($params as $p) {
      if (is_array($p)) {
        $arr = array_merge($arr, array_values($p));
        $values .= rtrim(str_repeat('?,', count($p)), ',') . '),(';
      } else {
        $arr[] = $p;
        $values .= '?,';
      }
    }

    $q .= ' (' . implode(',', $cols) . ') VALUES (' . rtrim(rtrim($values, '),('), ',') . ')';

    return (int) self::execute_query($q, $arr)['id'];
  }

  private static function dbUpdate($table, $set, $params = [])
  {
    self::init();

    return self::execute_query(
      'UPDATE ' . $table . ' SET ' . $set,
      $params
    )['query']->RowCount();
  }

  protected static function update(array $update_data, string $query, array $query_params = []): int
  {
    $q = '';
    $params = [];

    foreach ($update_data as $col => $val) {
      $q .= $col . '=?,';
      $params[] = $val;
    }

    return self::dbUpdate(static::TABLE, rtrim($q, ',') . $query, array_merge($params, $query_params));
  }

  /**
   * @param array $cols array of columns
   * @param array $params array of values. Can be multilayered for saving multiple rows (e.g. [[val1, val2, val3], [val1, val2, val3]])
   * @param array $options array of options to apply to the insert
   * @throws \Exception
   */
  protected static function saveArray(array $cols, array $params, array $options = []): int
  {
    if (in_array(static::ID, $cols))
      throw new \Exception('Cannot save the ID column...');

    return self::dbInsert(static::TABLE, $cols, $params, $options);
  }

  protected static function getRecords(string $sql = '', array $params = [], array $cols = ['*']): array
  {
    return self::dbSelect(
      static::TABLE,
      implode(',', $cols),
      $sql,
      $params
    );
  }

  protected static function in(string $col, array $values): string
  {
    return $col . ' IN (' . trim(implode(',', array_fill(0, count($values), '?'))) . ')';
  }

  protected static function equals(string $col): string
  {
    return $col . '=?';
  }

  protected static function leftJoin(string $col2): string
  {
    return ' LEFT JOIN ' . static::TABLE . ' ON ' . static::TABLE . '.' . static::ID . '=' . $col2;
  }

  protected static function where(string $sql): string
  {
    return ' WHERE ' . $sql;
  }

  protected static function and(array $queries): string
  {
    return '(' . implode(' AND ', $queries) . ')';
  }
}
