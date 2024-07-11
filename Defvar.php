<?php

namespace IPP\Student;
use IPP\Student\Exceptions\SemanticCtrl_Exception;

class Defvar {

  use InstrTrait;

  public function execute(string $var_name, Frame $frame) : void {
    if (!$frame->variableExists($var_name)) {
      $frame->setVariable($var_name, null, null);
    } else {
      throw new SemanticCtrl_Exception("Semantic error in Defvar instruction.");
    }
    
  }
}
