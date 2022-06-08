<?php

namespace XQ\Drivers\Message\Trace;

class SassTrace
{
  /** @var string */
  public $file;
  /** @var int */
  public $line;
  /** @var int */
  public $column;
  /** @var string */
  public $description;

  public function __toString()
  {
    // Do a default description if appropriate
    if (!isset($this->description))
    {
      $this->description = '';
      if (isset($this->file))
      {
        try
        {
          $this->description = $this->isImportFile($this->file) ? '@import' : 'root stylesheet';
        }
        catch (\Exception $e)
        {
          $this->description = '';
        }
      }
    }

    // return the string
    return sprintf(
      '%s %s:%s %s',
      $this->file   ?? 'Unknown File',
      $this->line   ?? 0,
      $this->column ?? 0,
      $this->description
    );
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
