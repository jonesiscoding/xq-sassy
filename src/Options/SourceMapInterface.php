<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

interface SourceMapInterface
{
  const DEFAULT_SOURCEMAP = false;

  /**
   * @return bool
   */
  public function isSourceMap(): bool;

  /**
   * @param bool $sourceMap
   *
   * @return AbstractSassDriver|SourceMapInterface
   */
  public function setSourceMap(bool $sourceMap): AbstractSassDriver;
}