<?php
/**
 * AbstractSassDriver.php
 */

namespace XQ\Drivers;

/**
 * Class AbstractSassDriver
 * @package XQ\Drivers
 */
abstract class AbstractSassDriver
{
  const STYLE_NESTED = "nested";
  const STYLE_EXPANDED = "expanded";
  const STYLE_COMPACT = "compact";
  const STYLE_COMPRESSED = "compressed";
  const DEFAULT_PRECISION = 5;
  const DEFAULT_SOURCEMAP = false;
  const DEFAULT_MAPCOMMENT = true;
  const DEFAULT_LINENUMBERS = false;
  const DEFAULT_STYLE = "nested";

  // CLI Options
  protected $importPaths;
  protected $pluginPaths;
  protected $sourceMap;
  protected $mapComment;
  protected $lineNumbers;
  protected $precision;
  protected $style;

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
  abstract public function compile( $content );

  // endregion ///////////////////////////////////////////// End Main Public Methods

  // region //////////////////////////////////////////////// CLI Arguments

  /**
   * Sets the output style between the various output styles supported by SASS.
   *
   * @param int $style    One of the output style constants from this class.
   *
   * @return $this
   * @throws \Exception   If an invalid output style is specified.
   */
  public function setOutputStyle( $style )
  {
    $possibleStyles = array(
      self::STYLE_NESTED,
      self::STYLE_EXPANDED,
      self::STYLE_COMPACT,
      self::STYLE_COMPRESSED
    );

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
  public function setImportPaths( array $paths )
  {
    $this->importPaths = array();
    $this->addImportPath( $paths );

    return $this;
  }

  /**
   * Resets the plugin paths to their default, then adds the given paths.
   *
   * @param array|string $paths A list of the possible paths to check for SASS plugins.
   *
   * @return $this
   */
  public function setPluginPaths( array $paths )
  {
    $this->pluginPaths = array();
    $this->addPluginPath( $paths );
  }

  public function addImportPath($path, $prepend = false)
  {
    $paths = (is_array($path)) ? $path : array($path);

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

  /**
   * @param array|string  $path     A path or array of paths to add to the list of plugin paths.
   * @param bool          $prepend  Whether or not to add this path to the beginning of the list.
   *
   * @return $this
   */
  public function addPluginPath($path, $prepend = false)
  {
    $paths = (is_array($path)) ? $path : array($path);

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
   * Sets whether or not to create a source map file (if supported by the compiler)
   *
   * @param bool $sourceMap
   */
  public function setSourceMap( $sourceMap )
  {
    $this->sourceMap = $sourceMap;
  }

  /**
   * Sets whether or not to suppress comments in the map file.
   *
   * @param bool $mapComment
   */
  public function setMapComment( $mapComment )
  {
    $this->mapComment = $mapComment;
  }

  /**
   * @param bool $lineNumbers
   */
  public function setLineNumbers( $lineNumbers )
  {
    $this->lineNumbers = $lineNumbers;
  }

  /**
   * @param $precision
   */
  public function setPrecision( $precision )
  {
    $this->precision = $precision;
  }

  // endregion ///////////////////////////////////////////// End CLI Arguments

  // region //////////////////////////////////////////////// Helper Methods

  /**
   * Sets the object's properties back to their defaults.
   */
  protected function setDefaults()
  {
    $this->importPaths = array();
    $this->pluginPaths = array();
    $this->sourceMap = self::DEFAULT_SOURCEMAP;
    $this->mapComment = self::DEFAULT_MAPCOMMENT;
    $this->lineNumbers = self::DEFAULT_LINENUMBERS;
    $this->precision = self::DEFAULT_PRECISION;
    $this->style = self::DEFAULT_STYLE;
  }

  // endregion ///////////////////////////////////////////// End Helper Methods

}