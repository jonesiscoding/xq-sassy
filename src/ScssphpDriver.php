<?php

/**
 * ScssphpDriver.php
 *
 * @noinspection PhpUndefinedClassInspection PhpUndefinedNamespaceInspection
 */

namespace XQ\Drivers;


/**
 * PHP Driver to normalize usage of the scssphp/scssphp package.  For more info about the PHP Sass extension, see it's repo
 * at https://github.com/scssphp/scssphp.
 *
 * Class ScssphpDriver
 *
 * @author  Aaron M Jones <aaron@jonesiscoding.com>
 * @version xqSassy v2.0 (https://github.com/xq-sassy/pleasing)
 * @license MIT (https://github.com/jonesiscoding/xq-sassy/blob/master/LICENSE)
 *
 * @package XQ\Drivers
 */
class ScssphpDriver extends AbstractSassDriver
{
  // region //////////////////////////////////////////////// Main Public Methods

  /**
   * @param string $content
   *
   * @return string|null
   * @throws \Exception
   */
  public function compile(string $content)
  {
    if(class_exists("\\ScssPhp\\ScssPhp\\Compiler"))
    {
      if (!empty($content))
      {
        // Create the ProcessBuilder
        $sc = new \ScssPhp\ScssPhp\Compiler();

        // Import Paths
        foreach ($this->importPaths as $importPath)
        {
          $sc->addImportPath($importPath);
        }

        // Output Style
        if ($this->style != self::DEFAULT_STYLE)
        {
          switch ($this->style) {
            case self::STYLE_COMPRESSED:
              $sc->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
              break;
            case self::STYLE_EXPANDED:
            default:
              $sc->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::EXPANDED);
              break;
          }
        }

        $output = $sc->compileString($content)->getCss();
      }
    }
    else
    {
      throw new \LogicException("The package scssphp/scssphp must be required in composer.");
    }

    return (isset($output)) ? $output : null;
  }

  // endregion ///////////////////////////////////////////// End Main Public Methods
}
