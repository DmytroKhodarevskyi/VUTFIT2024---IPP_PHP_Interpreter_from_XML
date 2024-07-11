<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class XmlSemanticErr_Exception extends IPPException {

  public function __construct(string $message = "Xml semantic error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::INVALID_SOURCE_STRUCTURE, $previous, true);
    }
}