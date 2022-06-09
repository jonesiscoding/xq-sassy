<?php
/**
 * PhpSassDriver.php
 */

namespace XQ\Drivers;

use XQ\Drivers\Options\LineNumbersInterface;
use XQ\Drivers\Options\LineNumbersTrait;
use XQ\Drivers\Options\SourceMapInterface;
use XQ\Drivers\Options\SourceMapTrait;

/**
 * PHP Driver to normalize usage of the PHP SASS extension.  For more info about the PHP Sass extension, see it's repo
 * at https://github.com/sensational/sassphp.
 *
 * Class PhpSassDriver
 *
 * @author  Aaron M Jones <aaron@jonesiscoding.com>
 * @version xqSassy Sassy v2.0 (https://github.com/xq-sassy/pleasing)
 * @license MIT (https://github.com/jonesiscoding/xq-sassy/blob/master/LICENSE)
 *
 * @package XQ\Drivers;
 */
class PhpSassDriver extends AbstractSassDriver implements LineNumbersInterface, SourceMapInterface
{
  use LineNumbersTrait;
  use SourceMapTrait;

  // region //////////////////////////////////////////////// Main Public Methods


  /** @noinspection PhpUndefinedClassInspection */
  /**
   * @throws \Exception
   */
  public function compile(string $content)
  {
    if (!empty($content))
    {
      // Create the ProcessBuilder
      $Sass = new Sass();

      // Import Paths
      foreach ($this->importPaths as $importPath)
      {
        $Sass->setIncludePath($importPath);
      }

      // Line Numbers
      if ($this->lineNumbers)
      {
        $Sass->setComments(true);
      }

      // Output Style
      if ($this->style != self::DEFAULT_STYLE)
      {
        switch ($this->style) {
          case self::STYLE_NESTED:
            $Sass->setStyle(Sass::STYLE_NESTED);
            break;
          case self::STYLE_EXPANDED:
            $Sass->setStyle(Sass::STYLE_EXPANDED);
            break;
          case self::STYLE_COMPACT:
            $Sass->setStyle(Sass::STYLE_COMPACT);
            break;
          case self::STYLE_COMPRESSED:
            $Sass->setStyle(Sass::STYLE_COMPRESSED);
            break;
          default:
            $Sass->setStyle(Sass::STYLE_NESTED);
        }
      }

      try
      {
        $output = $Sass->compile($content);
      }
      catch (\SassException $e)
      {
        throw new \Exception($e->getMessage());
      }

      if (!$this->isSourceMap() && is_array($output))
      {
        $output = $output[0];
      }
    }

    return (isset($output)) ? $output : null;
  }

  // endregion ///////////////////////////////////////////// End Main Public Methods
}
