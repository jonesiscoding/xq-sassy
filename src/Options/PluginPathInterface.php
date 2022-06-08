<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

interface PluginPathInterface
{
  /**
   * @param string|array $plugin
   * @param bool   $prepend
   *
   * @return AbstractSassDriver|PluginPathInterface
   */
  public function addPluginPath($plugin, bool $prepend = false): AbstractSassDriver;

  /**
   * @param array $plugins
   *
   * @return AbstractSassDriver|PluginPathInterface
   */
  public function setPluginPaths(array $plugins): AbstractSassDriver;

  /**
   * @return string[]
   */
  public function getPluginPaths(): array;
}
