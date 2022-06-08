<?php

namespace XQ\Drivers\Options;

use XQ\Drivers\AbstractSassDriver;

trait PluginPathTrait
{
  /** @var string[] */
  protected $pluginPaths;

  /**
   * @param array|string  $path     A path or array of paths to add to the list of plugin paths.
   * @param bool          $prepend  Whether to add this path to the beginning of the list.
   *
   * @return AbstractSassDriver|PluginPathTrait|PluginPathInterface
   */
  public function addPluginPath($path, bool $prepend = false): AbstractSassDriver
  {
    $paths = (is_array($path)) ? $path : [$path];

    foreach ( $paths as $pluginPath )
    {
      if ( $realPluginPath = realpath( $pluginPath ) )
      {
        if ( $prepend )
        {
          array_unshift( $this->pluginPaths, $realPluginPath );
        }
        else
        {
          $this->pluginPaths[] = $realPluginPath;
        }
      }
    }

    return $this;
  }

  /**
   * @return string[]
   */
  public function getPluginPaths(): array
  {
    return $this->pluginPaths;
  }

  /**
   * Resets the plugin paths to their default, then adds the given paths.
   *
   * @param array|string $paths A list of the possible paths to check for SASS plugins.
   *
   * @return AbstractSassDriver|PluginPathTrait|PluginPathInterface
   */
  public function setPluginPaths(array $paths): AbstractSassDriver
  {
    $this->pluginPaths = [];
    $this->addPluginPath( $paths );

    return $this;
  }
}
