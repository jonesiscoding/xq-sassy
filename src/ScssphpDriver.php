<?php

/**
 * ScssphpDriver.php
 *
 * @noinspection PhpUndefinedClassInspection PhpUndefinedNamespaceInspection
 */

namespace XQ\Drivers;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Compact;
use Leafo\ScssPhp\Formatter\Compressed;
use Leafo\ScssPhp\Formatter\Expanded;
use Leafo\ScssPhp\Formatter\Nested;
use XQ\Drivers\Options\LineNumbersInterface;
use XQ\Drivers\Options\LineNumbersTrait;
use XQ\Drivers\Options\PrecisionInterface;
use XQ\Drivers\Options\PrecisionTrait;

/**
 * PHP Driver to normalize usage of the leafo/scssphp package.  For more info about the PHP Sass extension, see it's repo
 * at https://github.com/leafo/scssphp.
 *
 * Class ScssphpDriver
 *
 * @author  Aaron M Jones <aaron@jonesiscoding.com>
 * @version xqSassy Sassy v1.2 (https://github.com/xq-sassy/pleasing)
 * @license MIT (https://github.com/jonesiscoding/xq-sassy/blob/master/LICENSE)
 *
 * @package XQ\Drivers
 */
class ScssphpDriver extends AbstractSassDriver implements PrecisionInterface, LineNumbersInterface
{
  use PrecisionTrait;
  use LineNumbersTrait;

  // region //////////////////////////////////////////////// Main Public Methods

  public function compile($content)
  {
    if (!empty($content))
    {
      // Create the ProcessBuilder
      $sc = new Compiler();

      // Import Paths
      foreach ($this->importPaths as $importPath)
      {
        $sc->addImportPath($importPath);
      }

      // Line Numbers
      if ($this->isLineNumbers())
      {
        $sc->setLineNumberStyle(Compiler::LINE_COMMENTS);
      }

      // Precision
      $precision = $this->getPrecision();
      if ($precision != self::DEFAULT_PRECISION)
      {
        $sc->setNumberPrecision($precision);
      }

      // Output Style
      if ($this->style != self::DEFAULT_STYLE)
      {
        switch ($this->style) {
          case self::STYLE_EXPANDED:
            $sc->setFormatter(Expanded::class);
            break;
          case self::STYLE_COMPACT:
            $sc->setFormatter(Compact::class);
            break;
          case self::STYLE_COMPRESSED:
            $sc->setFormatter(Compressed::class);
            break;
          case self::STYLE_NESTED:
          default:
            $sc->setFormatter(Nested::class);
            break;
        }
      }

      $output = $sc->compile($content);
    }

    return (isset($output)) ? $output : null;
  }

  // endregion ///////////////////////////////////////////// End Main Public Methods
}
