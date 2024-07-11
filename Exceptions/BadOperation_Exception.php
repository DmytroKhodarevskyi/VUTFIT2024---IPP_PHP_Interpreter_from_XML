<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class BadOperation_Exception extends IPPException {

public function __construct(string $message = "Bad operation", ?Throwable $previous = null)
  {
      parent::__construct($message, ReturnCode::OPERAND_VALUE_ERROR, $previous, true);
  }
}