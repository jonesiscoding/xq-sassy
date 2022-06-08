<?php

namespace XQ\Drivers\Message;

class MessageFactory
{
  /**
   * @param $output
   *
   * @return AbstractMessage[]
   */
  public function build($output): array
  {
    // Make it easier to parse
    $message  = null;
    $messages = [];
    $parts    = explode("\n", $output);
    foreach ($parts as $line)
    {
      if (!empty($line))
      {
        if (preg_match(DebugMessage::PATTERN, $line))
        {
          $messages[] = (new DebugMessage())->addLine($line);

          continue;
        }
        elseif (preg_match('#^DEPRECATION WARNING#', $line))
        {
          $messages[] = $message = new DeprecationMessage();
        }
        elseif (preg_match('#^WARNING#', $line))
        {
          if (preg_match('#WARNING: \d+ repetitive#', $line))
          {
            continue;
          }

          $messages[] = $message = new WarningMessage();
        }
        elseif (preg_match('#^Error:#', $line))
        {
          $messages[] = $message = new ErrorMessage();
        }

        $message->addLine($line);
      }
    }

    return $messages;
  }
}
