<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

interface PrecisionInterface
{
  const DEFAULT_PRECISION = 5;

  public function getPrecision();

  /**
   * @param int $precision
   *
   * @return AbstractSassDriver|PrecisionInterface
   */
  public function setPrecision(int $precision): AbstractSassDriver;
}