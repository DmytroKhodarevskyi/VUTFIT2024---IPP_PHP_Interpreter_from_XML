<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class BadValue_Exception extends IPPException {

public function __construct(string $message = "Bad value", ?Throwable $previous = null)
  {
      parent::__construct($message, ReturnCode::VALUE_ERROR, $previous, true);
  }
}