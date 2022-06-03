<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;
use XQ\Drivers\Options\MapCommentInterface;

trait MapCommentTrait
{
  /** @var bool  */
  protected $mapComment = MapCommentInterface::DEFAULT_MAPCOMMENT;

  /**
   * @return bool
   */
  public function isMapComment(): bool
  {
    return $this->mapComment;
  }

  /**
   * Sets whether to suppress comments in the map file.
   *
   * @param bool $mapComment
   *
   * @return AbstractSassDriver|MapCommentInterface|MapCommentTrait
   */
  public function setMapComment( bool $mapComment ): AbstractSassDriver
  {
    $this->mapComment = $mapComment;

    return $this;
  }
}