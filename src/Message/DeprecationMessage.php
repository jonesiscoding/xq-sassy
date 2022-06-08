<?php

namespace XQ\Drivers\Message;

use XQ\Drivers\Message\Trace\SassTrace;

class DeprecationMessage extends WarningMessage
{
  const PATTERN_RECOMMENDATION = '#Recommendation: (.*)#';

  /** @var string  */
  protected $type = 'DEPRECATION WARNING';
  /** @var string */
  protected $more;
  /** @var string */
  protected $recommendation;
  /** @var array */
  protected $trace;

  public function getSeverity(): int
  {
    return E_USER_DEPRECATED;
  }

  /**
   * @return string
   */
  public function getMore(): string
  {
    return $this->more ?? $this->parse()->more;
  }

  /**
   * @return string
   */
  public function getRecommendation(): string
  {
    return $this->recommendation ?? $this->parse()->recommendation;
  }

  public function getTrace(): array
  {
    return $this->trace ?? $this->parse()->trace;
  }

  public function getTraceAsString(): string
  {
    return implode("\n", $this->getTrace());
  }

  /**
   * @return DeprecationMessage
   */
  protected function parse(): AbstractMessage
  {
    // Get the lines as a separate array to work with
    $lines = array_filter($this->lines);
    // Get the first element as the message, trimming whitespace
    $message = trim(array_shift($lines));
    if (preg_match($this->getFullPattern(), $message, $m))
    {
      // This pattern contains file, line, column
      $this->line   = $m['line']       ?? 0;
      $this->column = $m['column']     ?? 0;
      $this->file   = trim($m['file']) ?? 'Unknown File';
      // And the next line is the message
      $this->message = array_shift($lines);
      // Then we have a syntax
      $this->syntax = implode("\n", $this->extractSyntax($lines));

      // Create the trace from the line/column/file info gathered
      $SassTrace              = new SassTrace();
      $SassTrace->line        = $this->line;
      $SassTrace->column      = $this->column;
      $SassTrace->file        = $this->file;
      $SassTrace->description = $this->isImportFile($this->file) ? '@import' : 'root stylesheet';
      $this->trace            = [$SassTrace];
    }
    elseif (preg_match($this->getPattern(), $message, $m))
    {
      // This pattern only contains a message
      $this->message = $m['message'] ?? 'Unknown Deprecation Warning';
      // Then we have a recommendation
      $this->recommendation = implode("\n", $this->extractRecommendation($lines));
      // Then more info
      $this->more = implode("\n", $this->extractMoreInfo($lines));
      // Then a syntax
      $this->syntax = implode("\n", $this->extractSyntax($lines));
      // Then a trace
      $this->trace = $this->extractTrace($lines);

      if (!empty($this->trace))
      {
        // Get the file, line, column from the first line of the trace
        $this->parseTrace(reset($this->trace));
      }
    }

    return $this;
  }

  protected function extractRecommendation($lines): array
  {
    $retval = [];
    foreach ($lines as $line)
    {
      if (preg_match(DeprecationMessage::PATTERN_RECOMMENDATION, $line, $matches))
      {
        $retval[] = $matches[1];
      }
    }

    return $retval;
  }

  /**
   * @param string[] $lines
   *
   * @return string[]
   */
  protected function extractMoreInfo(array $lines): array
  {
    $retval = [];
    foreach ($lines as $line)
    {
      if (!empty($line))
      {
        if (!preg_match(AbstractMessage::PATTERN_SYNTAX, $line))
        {
          if (!preg_match(AbstractMessage::PATTERN_TRACE, $line))
          {
            if (!preg_match(self::PATTERN_RECOMMENDATION, $line))
            {
              $retval[] = $line;
            }
          }
        }
      }
    }

    return $retval;
  }

  /**
   * @param string $file
   *
   * @return bool
   */
  protected function isImportFile(string $file): bool
  {
    return 0 === strpos(pathinfo($file, PATHINFO_BASENAME), '_');
  }
}
