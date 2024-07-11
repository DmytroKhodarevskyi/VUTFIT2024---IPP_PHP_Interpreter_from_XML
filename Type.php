<?php

namespace IPP\Student;

use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\BadString_Exception;
class Type {

  use InstrTrait;
  use SymbTrait;
  private mixed $final_symb;
  private string $final_type;

  public function execute(Variable $var, Variable|string $symb, Frames $frames, string $type) : void {

    $is_var = false;

    if (is_object($symb)) {
      list($this->final_symb, $this->final_type) = $this->parse_symb($symb, $frames, "Strlen");
      $is_var = true;
    } else {
      $this->final_symb = $symb;
      $this->final_type = $type;
    }

    $frame = $var->frame;
    $frame = $var->checkFrames($frame, $frames); 

    if (!$var->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Type.");
    }

    if ($is_var) {
      if ($this->final_type == "nil" && !$frame->isInitialized($symb->name)) {
        $frame->setVariable($var->name, "", "string");
        return;
      }
    }

    if ($this->final_symb == "nil") {
      $frame->setVariable($var->name, "nil", "string");
      return;
    }

    $result = null;

    if ($this->final_type == "string") {
      $result = "string";
    } else if ($this->final_type == "int") {
      $result = "int";
    } else if ($this->final_type == "bool") {
      $result = "bool";
    } else {
      throw new BadType_Exception("Bad type in Type instruction.");
    }

    $frame->setVariable($var->name, $result, "string");
  }

  /**
   * Parse symb override (do not exit if variable does not exist)
   * 
   * @param Variable $symb
   * @param Frames $frames
   * @param string $instr_name
   * 
   * @return string[]
   */
  public function parse_symb(Variable $symb, Frames $frames, string $instr_name) : array {

    $frame = $symb->frame;
    $name = $symb->name;

    $frame = $symb->checkFrames($frame, $frames);

    return [$frame->getVariable($name), $frame->getVariableType($name)];
  }
  
}
