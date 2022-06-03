<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;
use XQ\Drivers\Options\LineNumbersInterface;

trait LineNumbersTrait
{
  /** @var bool */
  protected $lineNumbers = LineNumbersInterface::DEFAULT_LINENUMBERS;

  /**
   * @return bool
   */
  public function isLineNumbers(): bool
  {
    return $this->lineNumbers;
  }

  /**
   * @param bool $lineNumbers
   *
   * @return AbstractSassDriver|LineNumbersTrait|LineNumbersInterface
   */
  public function setLineNumbers(bool $lineNumbers): AbstractSassDriver
  {
    $this->lineNumbers = $lineNumbers;

    return $this;
  }
}