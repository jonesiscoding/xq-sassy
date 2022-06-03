<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

interface LineNumbersInterface
{
  const DEFAULT_LINENUMBERS = false;

  /**
   * @return bool
   */
  public function isLineNumbers(): bool;

  /**
   * @param bool $lineNumbers
   *
   * @return AbstractSassDriver|LineNumbersInterface
   */
  public function setLineNumbers(bool $lineNumbers): AbstractSassDriver;
}