<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;


class BadType_Exception extends IPPException {

public function __construct(string $message = "Bad type", ?Throwable $previous = null)
  {
      parent::__construct($message, ReturnCode::OPERAND_TYPE_ERROR, $previous, true);
  }
}