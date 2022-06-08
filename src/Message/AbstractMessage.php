<?php

namespace XQ\Drivers\Message;

use XQ\Drivers\Message\Trace\SassTrace;

abstract class AbstractMessage
{
  const PATTERN_TRACE  = '#^(.*)\s+(\d+):(\d+)\s+(.*)$#';
  const PATTERN_SYNTAX = '#^\s*(\d*)\s*(╷|│|╵)(.*)$#';

  /** @var string[] */
  protected $lines;
  /** @var string */
  protected $message;
  /** @var string */
  protected $file;
  /** @var int */
  protected $line;

  /**
   * @return string
   */
  abstract public function getType(): string;

  /**
   * @return int
   */
  abstract public function getSeverity(): int;

  /**
   * @return DebugMessage|WarningMessage|ErrorMessage|DeprecationMessage
   */
  abstract protected function parse(): AbstractMessage;

  /**
   * @param $line
   *
   * @return $this
   */
  public function addLine($line): AbstractMessage
  {
    $this->lines[] = $line;

    return $this;
  }

  /**
   * @return string
   */
  public function getMessage(): string
  {
    return $this->message ?? $this->parse()->message;
  }

  /**
   * @return string
   */
  public function getFile(): string
  {
    return $this->file ?? $this->parse()->file;
  }

  /**
   * @return int
   */
  public function getLine(): int
  {
    return $this->line ?? $this->parse()->line;
  }

  /**
   * @return string
   */
  public function getSyntax(): string
  {
    return $this->syntax ?? $this->parse()->syntax;
  }

  public function getTrace(): array
  {
    return ['line' => $this->getLine(), 'file' => $this->getFile()];
  }

  public function getTraceAsString(): string
  {
    return sprintf('%s on line %s of %s', $this->getType(), $this->getLine(), $this->getFile());
  }

  public function getSummary(): string
  {
    return sprintf('%s in %s:L%s: %s', $this->getType(), $this->getFile(), $this->getLine(), $this->getMessage());
  }

  public function __toString()
  {
    return sprintf('%s:L%s', $this->getFile(), $this->getLine());
  }

  // region //////////////////////////////////////////////// Parsing Functions

  /**
   * @param $lines
   *
   * @return SassTrace[]
   */
  protected function extractTrace($lines): array
  {
    $tLines = array_filter($lines, function ($value) {
      return preg_match(AbstractMessage::PATTERN_TRACE, $value);
    });
    $retval = [];

    foreach ($tLines as $tLine)
    {
      if (!empty($tLine))
      {
        $retval[] = $SassTrace = new SassTrace();
        if (preg_match(AbstractMessage::PATTERN_TRACE, $tLine, $matches))
        {
          $SassTrace->file        = trim($matches[1]) ?? 'Unknown File';
          $SassTrace->line        = $matches[2]       ?? 0;
          $SassTrace->column      = $matches[3]       ?? 0;
          $SassTrace->description = $matches[4]       ?? '';
        }
      }
    }

    return $retval;
  }

  protected function extractSyntax($lines): array
  {
    return array_filter($lines, function ($value) {
      return preg_match(AbstractMessage::PATTERN_SYNTAX, $value);
    });
  }

  /**
   * @param SassTrace $SassTrace
   *
   * @return $this
   */
  protected function parseTrace(SassTrace $SassTrace): AbstractMessage
  {
    if (property_exists($this, 'file') && !isset($this->file))
    {
      $this->file = $SassTrace->file ?? 'Unknown File';
    }

    if (property_exists($this, 'line') && !isset($this->line))
    {
      $this->line = $SassTrace->line ?? 0;
    }

    if (property_exists($this, 'column') && !isset($this->column))
    {
      $this->column = $SassTrace->column ?? 0;
    }

    return $this;
  }
}
