<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

/**
 * Class PrecisionTrait
 * @package XQ\Drivers
 */
trait PrecisionTrait
{
  protected $precision;

  /**
   * @return int
   */
  public function getPrecision(): int
  {
    return $this->precision;
  }

  /**
   * @param int $precision
   *
   * @return AbstractSassDriver|PrecisionTrait|PrecisionInterface
   */
  public function setPrecision(int $precision): AbstractSassDriver
  {
    $this->precision = $precision;

    return $this;
  }
}
