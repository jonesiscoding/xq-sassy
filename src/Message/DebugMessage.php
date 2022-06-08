<?php

namespace XQ\Drivers\Message;

class DebugMessage extends AbstractMessage
{
  const PATTERN = '#^(?P<file>.*):(?P<line>\d+) DEBUG: (?P<value>.*)$#';

  public function getType(): string
  {
    return "DEBUG";
  }

  public function getCode(): int
  {
    return 0;
  }

  public function getSeverity(): int
  {
    return E_USER_NOTICE;
  }

  public function getSummary(): string
  {
    return $this->getMessage();
  }

  /**
   * @return AbstractMessage|DebugMessage
   */
  protected function parse(): AbstractMessage
  {
    $line = $this->getLine();
    if (preg_match(static::PATTERN, $line, $matches))
    {
      $this->line    = $matches['line'];
      $this->file    = $matches['file'];
      $this->message = sprintf('DEBUG from L:%s of %s: %s', $this->line, $this->file, $matches['value']);
    }

    return $this;
  }
}
