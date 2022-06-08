<?php

namespace XQ\Drivers\Message;

class DartSassException extends \ErrorException
{
  public function __construct(AbstractMessage $ErrorMessage, $code = null, \Throwable $previous = null)
  {
    $message = "";
    if ($ErrorMessage instanceof DebugMessage)
    {
      $message = $ErrorMessage->getMessage();
    }
    elseif ($ErrorMessage instanceof ErrorMessage || $ErrorMessage instanceof WarningMessage)
    {
      $syntax = str_replace(['╷', '╵'], ['│', '│'], $ErrorMessage->getSyntax());

      if ($ErrorMessage instanceof DeprecationMessage)
      {
        $message = sprintf("%s: %s", $ErrorMessage->getType(), $ErrorMessage->getMessage());
        $suggest = $ErrorMessage->getRecommendation();
        $other   = $ErrorMessage->getMore();

        if (!empty($suggest))
        {
          $message .= "\n\nRecommended Change: ".$suggest;
        }

        if (!empty($other))
        {
          $message .= "\n\n".$other;
        }

        $message .= sprintf("\n\n%s\n\n%s", $syntax, $ErrorMessage->getTraceAsString());
      }
      else
      {
        $message = sprintf("%s: %s", $ErrorMessage->getType(), $ErrorMessage->getMessage());

        if (!empty($syntax))
        {
          $message .= "\n\n".$syntax;
        }

        $message .= "\n\n".$ErrorMessage->getTraceAsString();
      }
    }

    parent::__construct(
      $message,
      $code ?? $ErrorMessage->getCode(),
      $ErrorMessage->getSeverity(),
      $ErrorMessage->getFile(),
      $ErrorMessage->getLine(),
      $previous
    );
  }
}
