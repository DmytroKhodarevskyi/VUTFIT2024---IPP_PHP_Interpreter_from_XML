<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class BadString_Exception extends IPPException {

public function __construct(string $message = "Bad string", ?Throwable $previous = null)
  {
      parent::__construct($message, ReturnCode::STRING_OPERATION_ERROR, $previous, true);
  }
}