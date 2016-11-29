<?php
/**
 * PhpSassDriver.php
 */

namespace XQ\Drivers;

/**
 * PHP Driver to normalize usage of the PHP SASS extension.  For more info about the PHP Sass extension, see it's repo
 * at https://github.com/sensational/sassphp.
 *
 * Class PhpSassDriver
 *
 * @author  Aaron M Jones <aaron@jonesiscoding.com>
 * @version ExactQuery Sassy v1.0.8 (https://github.com/xq-sassy/pleasing)
 * @license MIT (https://github.com/exactquery/xq-sassy/blob/master/LICENSE)
 *
 * @package XQ\Drivers;
 */
class PhpSassDriver extends AbstractSassDriver
{
  // region //////////////////////////////////////////////// Main Public Methods

  public function compile( $content )
  {
    if ( !empty( $content ) )
    {
      // Create the ProcessBuilder
      $Sass = new Sass();

      // Import Paths
      foreach ( $this->importPaths as $importPath )
      {
        $Sass->setIncludePath( $importPath );
      }

      // Line Numbers
      if ( $this->lineNumbers )
      {
        $Sass->setComments( true );
      }

      // Output Style
      if ( $this->style != self::DEFAULT_STYLE )
      {
        switch ( $this->style )
        {
          case self::STYLE_NESTED:
            $Sass->setStyle( Sass::STYLE_NESTED );
            break;
          case self::STYLE_EXPANDED:
            $Sass->setStyle( Sass::STYLE_EXPANDED );
            break;
          case self::STYLE_COMPACT:
            $Sass->setStyle( Sass::STYLE_COMPACT );
            break;
          case self::STYLE_COMPRESSED:
            $Sass->setStyle( Sass::STYLE_COMPRESSED );
            break;
          default:
            $Sass->setStyle( Sass::STYLE_NESTED );
        }
      }

      try
      {
        $output = $Sass->compile( $content );
      }
      catch ( \SassException $e )
      {
        throw new \Exception( $e->getMessage() );
      }

      if ( !$this->sourceMap && is_array( $output ) )
      {
        $output = $output[0];
      }
    }

    return (isset($output)) ? $output : null;
  }

  public function addPluginPath( $path )
  {
    throw new \Exception( 'The PHP Sass extension does not support setting a plugin path.' );
  }

  public function setPluginPaths( array $paths )
  {
    $this->addPluginPath( $paths );
  }

  public function setMapComment( $mapComment )
  {
    throw new \Exception( 'The PHP Sass extension does not support omitting map comments.' );
  }

  public function setPrecision( int $precision )
  {
    throw new \Exception( 'The PHP Sass extension does not support setting a precision value' );
  }

  // endregion ///////////////////////////////////////////// End Main Public Methods

}