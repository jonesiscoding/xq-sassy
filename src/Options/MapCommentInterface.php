<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

interface MapCommentInterface
{
  const DEFAULT_MAPCOMMENT = true;

  /**
   * @return bool
   */
  public function isMapComment(): bool;

  /**
   * @param bool $mapComment
   *
   * @return AbstractSassDriver
   */
  public function setMapComment(bool $mapComment): AbstractSassDriver;
}
