<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class Success_Exception extends IPPException {

public function __construct(string $message = "Success", ?Throwable $previous = null)
  {
      parent::__construct($message, ReturnCode::OK, $previous, false);
  }
}