<?php
/**
 * AbstractSassDriver.php
 */

namespace XQ\Drivers;

use XQ\Drivers\Options\LineNumbersInterface;
use XQ\Drivers\Options\MapCommentInterface;
use XQ\Drivers\Options\PluginPathInterface;
use XQ\Drivers\Options\PrecisionInterface;
use XQ\Drivers\Options\SourceMapInterface;

/**
 * Base class to be extended by various SASS compiler drivers.
 *
 * Class AbstractSassDriver
 *
 * @author  Aaron M Jones <aaron@jonesiscoding.com>
 * @version xqSassy v1.2 (https://github.com/xq-sassy/pleasing)
 * @license MIT (https://github.com/jonesiscoding/xq-sassy/blob/master/LICENSE)
 *
 * @package XQ\Drivers
 */
abstract class AbstractSassDriver
{
  const STYLE_NESTED     = "nested";
  const STYLE_EXPANDED   = "expanded";
  const STYLE_COMPACT    = "compact";
  const STYLE_COMPRESSED = "compressed";
  const DEFAULT_STYLE    = "nested";

  // CLI Options
  protected $importPaths;
  protected $style;
  /** @var bool|int */
  protected $debug;

  /**
   * @param int|bool $debug
   */
  public function __construct($debug)
  {
    $this->debug = $debug;
  }

  public function __clone()
  {
    $this->setDefaults();
  }

  // region //////////////////////////////////////////////// Main Public Methods

  /**
   * This method must be implemented by classes that extend this class, and should take the input
   * of the SCSS/SASS string, then return the compiled CSS.
   *
   * @param string $content
   *
   * @return mixed
   */
  abstract public function compile(string $content);

  /**
   * @return bool|int
   */
  public function isDebug()
  {
    return $this->debug;
  }

  public function getDebugLevel()
  {
    if (!is_int($this->debug))
    {
      return $this->isDebug() ? 1 : 0;
    }

    return $this->debug;
  }

  // endregion ///////////////////////////////////////////// End Main Public Methods

  // region //////////////////////////////////////////////// CLI Arguments

  /**
   * Sets the output style between the various output styles supported by SASS.
   *
   * @param int $style One of the output style constants from this class.
   *
   * @return $this
   * @throws \Exception   If an invalid output style is specified.
   */
  public function setOutputStyle(int $style): AbstractSassDriver
  {
    $possibleStyles = [
      self::STYLE_NESTED,
      self::STYLE_EXPANDED,
      self::STYLE_COMPACT,
      self::STYLE_COMPRESSED
    ];

    if ( !in_array( $style, $possibleStyles ) )
    {
      throw new \Exception( 'Invalid output style specified.' );
    }

    $this->style = $style;

    return $this;
  }

  /**
   * Resets the input paths to their default, then adds the given paths.
   *
   * @param array|string $paths A list of the possible paths to import other SASS/SCSS files from.
   *
   * @return $this
   */
  public function setImportPaths(array $paths): AbstractSassDriver
  {
    $this->importPaths = [];
    $this->addImportPath( $paths );

    return $this;
  }

  public function addImportPath($path, $prepend = false)
  {
    $paths = (is_array($path)) ? $path : [$path];

    foreach ( $paths as $importPath )
    {
      if ( $realImportPath = realpath( $importPath ) )
      {
        if ( $prepend )
        {
          array_unshift( $this->importPaths, $realImportPath );
        }
        else
        {
          $this->importPaths[] = $realImportPath;
        }
      }
    }
  }

  // endregion ///////////////////////////////////////////// End CLI Arguments

  // region //////////////////////////////////////////////// Helper Methods

  /**
   * Sets the object's properties back to their defaults.
   */
  protected function setDefaults()
  {
    // Default the defaults
    $defaults = $this->getDefaults();

    $this->importPaths = $defaults['import_paths'] ?? [];
    $this->style       = $defaults['style']        ?? self::DEFAULT_STYLE;

    if ($this instanceof SourceMapInterface)
    {
      $this->setSourceMap($defaults['source_map'] ?? SourceMapInterface::DEFAULT_SOURCEMAP);
    }

    if ($this instanceof LineNumbersInterface)
    {
      $this->setLineNumbers($defaults['line_numbers'] ?? LineNumbersInterface::DEFAULT_LINENUMBERS);
    }

    if ($this instanceof PrecisionInterface)
    {
      $this->setPrecision($defaults['precision'] ?? PrecisionInterface::DEFAULT_PRECISION);
    }

    if ($this instanceof MapCommentInterface)
    {
      $this->setMapComment($defaults['map_comment'] ?? MapCommentInterface::DEFAULT_MAPCOMMENT);
    }

    if ($this instanceof PluginPathInterface)
    {
      $this->setPluginPaths($defaults['plugin_paths'] ?? []);
    }
  }

  protected function getDefaults(): array
  {
    return [];
  }

  // endregion ///////////////////////////////////////////// End Helper Methods
}
