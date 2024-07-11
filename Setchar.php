<?php

namespace IPP\Student;

use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\BadString_Exception;
class Setchar {

  use InstrTrait;
  use SymbTrait;
  private string $final_symb2;
  private string $final_type2;
  private string $final_symb3;
  private string $final_type3;

  public function execute(Variable $var, Variable|string $symb2, Variable|string $symb3, Frames $frames, string $type2, string $type3) : void {

    if (is_object($symb2)) {
      list($this->final_symb2, $this->final_type2) = $this->parse_symb($symb2, $frames, "Setchar");
    } else {
      $this->final_symb2 = $symb2;
      $this->final_type2 = $type2;
    }

    if (is_object($symb3)) {
      list($this->final_symb3, $this->final_type3) = $this->parse_symb($symb3, $frames, "Setchar");
    } else {
      $this->final_symb3 = $symb3;
      $this->final_type3 = $type3;
    }

    if ($this->final_type2 != "int" || $this->final_type3 != "string") {
      throw new BadType_Exception("Bad type in Setchar instruction.");
    }

    $frame = $var->frame;
    $frame = $var->checkFrames($frame, $frames); 
    
    if (!$var->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Setchar.");
    }

    $string_to_modify = $frame->getVariable($var->name);
    
    $type = $frame->getVariableType($var->name);
    if ($type != "string") {
      throw new BadType_Exception("Bad type in Setchar instruction.");
    }

    if ($this->final_symb2 < 0 || $this->final_symb2 > mb_strlen($string_to_modify) - 1) {
      throw new BadString_Exception("Bad type in Setchar instruction.");
    }

    if (mb_strlen($this->final_symb3) > 1) {
      $this->final_symb3 = mb_substr($this->final_symb3, 0, 1);
    }

    $string_to_modify[$this->final_symb2] = $this->final_symb3;

    // $string_to_modify = $string_to_modify[0];

    // if (gettype($string_to_modify) == "string") {
      $frame->setVariable($var->name, strval($string_to_modify), "string");
    // }
  }

  
}
