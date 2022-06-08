<?php

namespace XQ\Drivers\Message;

use XQ\Drivers\Message\Trace\SassTrace;

/**
 * Class WarningMessage
 * @package XQ\Drivers\Message
 */
class WarningMessage extends AbstractMessage
{
  /** @var string */
  protected $type = 'WARNING';
  /** @var int */
  protected $column;
  /** @var string */
  protected $syntax;
  /** @var SassTrace[] */
  protected $trace;

  public function __toString()
  {
    return $this->getSyntax();
  }

  public function getSeverity(): int
  {
    return E_USER_NOTICE;
  }

  public function getType(): string
  {
    return $this->type;
  }

  public function getCode(): int
  {
    return 0;
  }

  /**
   * @return int
   */
  public function getColumn(): int
  {
    return $this->column ?? $this->parse()->column ?? 0;
  }

  /**
   * @return string
   */
  public function getSyntax(): string
  {
    return $this->syntax ?? $this->parse()->syntax ?? '';
  }

  /**
   * @return string
   */
  public function getTraceAsString(): string
  {
    if (!empty($this->trace))
    {
      return implode("\n", $this->trace);
    }

    return sprintf('%s on line %s, column %s of %s.', $this->type, $this->getLine(), $this->getColumn(), $this->getFile());
  }

  /**
   * @return WarningMessage
   */
  protected function parse(): AbstractMessage
  {
    // Get the lines as a separate array to work with
    $lines = array_filter($this->lines);
    // Get the first element as the message, trimming whitespace
    $message = trim(array_shift($lines));
    // Populate based on pattern.
    if (preg_match($this->getFullPattern(), $message, $m))
    {
      // This pattern contains file, line, column
      $this->line   = $m['line']       ?? 0;
      $this->column = $m['column']     ?? 0;
      $this->file   = trim($m['file']) ?? 'Unknown File';
      // And the next line is the message
      $this->message = array_shift($lines);
      // From the remaining lines, we can pull the syntax
      $this->syntax = implode("\n", $this->extractSyntax($lines));
    }
    elseif (preg_match($this->getPattern(), $message, $m))
    {
      $this->message = $m['message'] ?? 'Unknown Warning';
      $this->trace   = $this->extractTrace($lines);
      // Probably don't have syntax, but doesn't hurt to try...
      $this->syntax = implode("\n", $this->extractSyntax($lines));

      if (!empty($this->trace))
      {
        $this->parseTrace(reset($this->trace));
      }
    }

    return $this;
  }

  protected function getPattern(): string
  {
    return sprintf('#^%s: (?P<message>.*)#', $this->getType());
  }

  protected function getFullPattern(): string
  {
    return sprintf('#^%s on line (?P<line>\d+), column (?P<column>\d+) of (?P<file>[^:]+):$#', $this->getType());
  }
}
