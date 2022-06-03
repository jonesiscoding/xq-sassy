<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

trait SourceMapTrait
{
  /** @var bool */
  protected $sourceMap = SourceMapInterface::DEFAULT_SOURCEMAP;

  /**
   * @return bool
   */
  public function isSourceMap(): bool
  {
    return $this->sourceMap;
  }

  /**
   * Sets whether to create a source map file (if supported by the compiler)
   *
   * @param bool $sourceMap
   * @return AbstractSassDriver|SourceMapTrait|SourceMapInterface
   */
  public function setSourceMap( bool $sourceMap ): AbstractSassDriver
  {
    $this->sourceMap = $sourceMap;

    return $this;
  }
}