<?php

namespace XQ\Drivers\Message;

use XQ\Drivers\Message\Trace\SassTrace;

class ErrorMessage extends AbstractMessage
{
  /** @var SassTrace[] */
  protected $trace;
  /** @var string */
  protected $syntax;

  public function getCode(): int
  {
    return 0;
  }

  public function getSeverity(): int
  {
    return E_USER_ERROR;
  }

  public function getType(): string
  {
    return "ERROR";
  }

  public function getTrace(): array
  {
    return $this->trace ?? $this->parse()->getTrace();
  }

  public function getTraceAsString(): string
  {
    return implode("\n", $this->getTrace());
  }

  /**
   * @return AbstractMessage|ErrorMessage
   */
  protected function parse(): AbstractMessage
  {
    // Remove Empty Lines
    $lines = array_filter($this->lines);
    // Get Just the Message
    $this->message = preg_replace('#^Error: (.*)$#', '$1', array_shift($lines));
    // Extract the Trace into an array of SassTrace objects
    $this->trace = $this->extractTrace($lines);
    // Extract the Syntax
    $this->syntax = implode("\n", $this->extractSyntax($lines));
    // Since the error message doesn't provide file/line, assign those from the first trace item.
    $this->parseTrace(reset($this->trace));

    return $this;
  }
}
