<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class XmlFormatErr_Exception extends IPPException {

public function __construct(string $message = "Xml format error", ?Throwable $previous = null)
  {
      parent::__construct($message, ReturnCode::INVALID_XML_ERROR, $previous, true);
  }
}