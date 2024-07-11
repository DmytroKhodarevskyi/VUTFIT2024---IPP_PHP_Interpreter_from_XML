<?php

namespace IPP\Student;

use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\BadString_Exception;
class Strlen {

  use InstrTrait;
  use SymbTrait;
  private mixed $final_symb;
  private string $final_type;

  public function execute(Variable $var, Variable|string $symb, Frames $frames, string $type) : void {

    if (is_object($symb)) {
      list($this->final_symb, $this->final_type) = $this->parse_symb($symb, $frames, "Strlen");
    } else {
      $this->final_symb = $symb;
      $this->final_type = $type;
    }

    if ($this->final_type != "string") {
      throw new BadType_Exception("Bad type in Strlen instruction.");
    }

    $length = mb_strlen($this->final_symb);

    $frame = $var->frame;
    $frame = $var->checkFrames($frame, $frames); 
    
    if (!$var->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Strlen.");
    }

    $frame->setVariable($var->name, $length, "int");
  }

  
}
