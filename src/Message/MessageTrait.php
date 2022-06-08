<?php

namespace XQ\Drivers\Message;

trait MessageTrait
{
  protected $lines;
  protected $message;
  protected $file;
  protected $line;

  /**
   * @return AbstractMessage|ErrorMessage
   */
  abstract protected function parse();

  public function addLine($line)
  {
    $this->lines[] = $line;
  }

  public function getMessage(): string
  {
    return $this->message ?? $this->parse()->getMessage();
  }

  public function getFile(): string
  {
    return $this->file ?? $this->parse()->getFile();
  }

  public function getLine(): int
  {
    return $this->line ?? $this->parse()->getLine();
  }

  /**
   * @return string
   */
  public function getSyntax(): string
  {
    return $this->syntax ?? $this->parse()->getSyntax();
  }
}
