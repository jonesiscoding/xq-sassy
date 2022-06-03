<?php

namespace XQ\Drivers;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use XQ\Drivers\Options\SourceMapInterface;
use XQ\Drivers\Options\SourceMapTrait;

class DartSassDriver extends AbstractSassDriver implements SourceMapInterface
{
  use SourceMapTrait;

  /** @var string */
  protected $sassPath;
  protected $tmpPath;

  public function __construct($sassPath, $tmpPath = '/tmp')
  {
    $this->sassPath = $sassPath;
    $this->tmpPath = $tmpPath . DIRECTORY_SEPARATOR . 'xq_sass';
    $this->setDefaults();
  }

  public function compile($content)
  {
    if ( !empty( $content ) )
    {
      // Input and Output
      $files = $this->getTmpFiles();
      file_put_contents( $files[ 'input' ], $content );

      // Arguments
      $args = $this->buildArgs( $files[ 'input' ], $files[ 'output' ] );
      array_unshift( $args, $this->sassPath );

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

  private function buildArgs($input, $output)
  {
    // Import Paths
    foreach ( $this->importPaths as $importPath )
    {
      $args[] = '--load-path';
      $args[] = $importPath;
    }

    // Source Map
    if ( !$this->isSourceMap() )
    {
      $args[] = '--no-source-map';
    }

    // Output Style
    if ( $this->style != self::DEFAULT_STYLE )
    {
      $args[] = "--style";
      $args[] = $this->style;
    }

    $args[] = '--no-error-css';
    $args[] = '--no-color';

    // Input & Output
    $args[] = $input;
    $args[] = $output;

    return $args;
  }

  protected function getDefaults()
  {
    return array('source_map' => true);
  }


  private function getTmpFiles()
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

    return array(
        'input'  => $this->tmpPath . DIRECTORY_SEPARATOR . 'in' . $unique,
        'output' => $this->tmpPath . DIRECTORY_SEPARATOR . 'out' . $unique
    );
  }

  private function cleanup( $files )
  {
    foreach ( $files as $file )
    {
      if(file_exists($file)) {
        unlink( $file );
      }
    }
  }
}