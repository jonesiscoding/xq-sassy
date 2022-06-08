<?php

namespace XQ\Drivers;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use XQ\Drivers\Message\DartSassException;
use XQ\Drivers\Message\DeprecationMessage;
use XQ\Drivers\Message\ErrorMessage;
use XQ\Drivers\Message\MessageFactory;
use XQ\Drivers\Message\WarningMessage;
use XQ\Drivers\Options\SourceMapInterface;
use XQ\Drivers\Options\SourceMapTrait;

class DartSassDriver extends AbstractSassDriver implements SourceMapInterface
{
  use SourceMapTrait;

  /** @var string */
  protected $sassPath;
  protected $tmpPath;

  public function __construct($debug = false, $sassPath = null, $tmpPath = '/tmp')
  {
    parent::__construct($debug);

    $this->sassPath = $sassPath;
    $this->tmpPath  = $tmpPath . DIRECTORY_SEPARATOR . 'xq_sass';
    $this->setDefaults();
  }

  /**
   * @param string $content
   *
   * @return false|string|null
   * @throws DartSassException
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
      array_unshift( $args, $this->sassPath );

      // Run the Process
      $Process = new Process( $args );
      $Process->run();

      // Check for Errors
      $messages = (new MessageFactory())->build($Process->getErrorOutput());
      if (!$Process->isSuccessful() || !file_exists($files['output']))
      {
        $this->cleanup( $files );

        foreach ($messages as $SassMessage)
        {
          if ($SassMessage instanceof ErrorMessage)
          {
            $prev = $Process->isSuccessful() ? null : new ProcessFailedException($Process);

            throw new DartSassException($SassMessage, $Process->getExitCode(), $prev);
          }
        }

        throw new ProcessFailedException($Process);
      }
      else
      {
        $output = file_get_contents($files['output']);

        // Clean Up
        $this->cleanup($files);

        foreach ($messages as $SassMessage)
        {
          if ($SassMessage instanceof ErrorMessage)
          {
            throw new DartSassException($SassMessage, $Process->getExitCode());
          }
          elseif ($SassMessage instanceof DeprecationMessage && $this->isDebug())
          {
            if ($this->getDebugLevel() > 1)
            {
              throw new DartSassException($SassMessage, $Process->getExitCode());
            }

            @trigger_error($SassMessage->getSummary(), $SassMessage->getSeverity());
          }
          elseif ($SassMessage instanceof WarningMessage && $this->isDebug())
          {
            if ($this->getDebugLevel() > 1)
            {
              throw new DartSassException($SassMessage, $Process->getExitCode());
            }

            @trigger_error($SassMessage->getSummary(), $SassMessage->getSeverity());
          }
        }
      }
    }

    return (isset($output)) ? $output : null;
  }

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

  protected function getDefaults(): array
  {
    return ['source_map' => true];
  }

  /**
   * @return string[]
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
}
