<?php
/**
 * SasscDriver.php
 */

namespace XQ\Drivers;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use XQ\Drivers\Options as Options;

/**
 * PHP Driver for the 'sassc' binary.  For more info on sassc, see https://github.com/sass/sassc.
 *
 * Class SasscDriver
 *
 * @author  Aaron M Jones <aaron@jonesiscoding.com>
 * @version xqSassy v2.0 (https://github.com/xq-sassy/pleasing)
 * @license MIT (https://github.com/jonesiscoding/xq-sassy/blob/master/LICENSE)
 *
 * @package XQ\Drivers
 */
class SasscDriver extends AbstractSassDriver implements Options\MapCommentInterface, Options\PluginPathInterface, Options\PrecisionInterface, Options\LineNumbersInterface, Options\SourceMapInterface
{
  use Options\LineNumbersTrait;
  use Options\MapCommentTrait;
  use Options\PluginPathTrait;
  use Options\PrecisionTrait;
  use Options\SourceMapTrait;

  protected $sasscPath;
  protected $tmpPath;

  public function __construct($debug = false, $sasscPath = null, $tmpPath = '/tmp')
  {
    parent::__construct($debug);

    $this->sasscPath = $sasscPath;
    $this->tmpPath   = $tmpPath . DIRECTORY_SEPARATOR . 'xq_sassc';
    $this->setDefaults();
  }

  // region //////////////////////////////////////////////// Main Public Methods

  /**
   * Compiles the given SCSS/SASS content into CSS, using 'sassc'.
   *
   * @param string $content
   *
   * @return null|string
   * @throws \Exception
   */
  public function compile(string $content)
  {
    if ( !empty( $content ) )
    {
      // Input and Output
      $files = $this->getTmpFiles();
      file_put_contents( $files[ 'input' ], $content );

      // Arguments
      $args = $this->buildArgs( $files[ 'input' ], $files[ 'output' ] );
      array_unshift( $args, $this->sasscPath );

      // Run the Process
      $Process = new Process( $args );
      $Process->run();

      // Check for Errors
      if (!$Process->isSuccessful())
      {
        $this->cleanup( $files );
        throw new ProcessFailedException($Process);
      }
      else
      {
        if ( file_exists( $files[ 'output' ] ) )
        {
          $output = file_get_contents( $files[ 'output' ] );
        }
        else
        {
          throw new \Exception( 'No output from compiled SASS (' . $Process->getOutput() . ')' );
        }
      }

      // Clean Up
      $this->cleanup($files);
    }

    return (isset($output)) ? $output : null;
  }

  // endregion ///////////////////////////////////////////// End Main Public Methods

  // region //////////////////////////////////////////////// Private Helper Methods

  /**
   * @param string $input
   * @param string $output
   *
   * @return array
   */
  private function buildArgs(string $input, string $output): array
  {
    // Import Paths
    foreach ( $this->importPaths as $importPath )
    {
      $args[] = '--load-path';
      $args[] = $importPath;
    }

    // Plugin Paths
    foreach ( $this->getPluginPaths() as $pluginPath )
    {
      $args[] = "--plugin-path";
      $args[] = $pluginPath;
    }

    // Source Map
    if ( $this->isSourceMap())
    {
      $args[] = '--sourcemap';
    }

    // Map Comment
    if ( !$this->isMapComment() )
    {
      $args[] = '--omit-map-comment';
    }

    // Line Numbers
    if ( $this->isLineNumbers() )
    {
      $args[] = '--line-numbers';
    }

    // Precision
    $precision = $this->getPrecision();
    if ($precision != self::DEFAULT_PRECISION )
    {
      $args[] = '--precision';
      $args[] = $precision;
    }

    // Output Style
    if ( $this->style != self::DEFAULT_STYLE )
    {
      $args[] = "--style";
      $args[] = $this->style;
    }

    // Input & Output
    $args[] = $input;
    $args[] = $output;

    return $args;
  }

  /**
   * @throws \Exception
   */
  private function getTmpFiles(): array
  {
    if ( !file_exists( $this->tmpPath ) )
    {
      if ( !@mkdir( $this->tmpPath, 0777, true ) )
      {
        throw new \Exception( 'The temporary path "' . $this->tmpPath . '" could not be created.' );
      }
    }
    elseif ( !is_dir( $this->tmpPath ) || !is_writable( $this->tmpPath ) )
    {
      throw new \Exception( 'The temporary path ' . $this->tmpPath . ' exists, but is not a writable directory.' );
    }

    $unique = uniqid();

    return [
        'input'  => $this->tmpPath . DIRECTORY_SEPARATOR . 'in' . $unique,
        'output' => $this->tmpPath . DIRECTORY_SEPARATOR . 'out' . $unique
    ];
  }

  private function cleanup($files)
  {
    foreach ( $files as $file )
    {
      if (file_exists($file))
      {
        unlink( $file );
      }
    }
  }

  // endregion ///////////////////////////////////////////// End Private Helper Methods
}
