<?php

namespace IPP\Student\Exceptions;
use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class SemanticCtrl_Exception extends IPPException {

public function __construct(string $message = "Semantic control error", ?Throwable $previous = null)
  {
      parent::__construct($message, ReturnCode::SEMANTIC_ERROR, $previous, true);
  }
}