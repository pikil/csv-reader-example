<?php

declare(strict_types=1);

namespace Pikil\CsvReader\Utils;

final class ResultResponse
{
  private const KEY_STATUS = 'status';
  private const KEY_TEXT = 'text';
  private const KEY_DATA = 'data';

  public function __construct(private bool $status, private string $text, private array $data) {}

  public function compose(): array
  {
    return [
      self::KEY_STATUS => $this->status,
      self::KEY_TEXT => $this->text,
      self::KEY_DATA => $this->data
    ];
  }
}
