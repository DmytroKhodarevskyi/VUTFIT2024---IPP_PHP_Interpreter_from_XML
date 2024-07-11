<?php

namespace IPP\Student;

use IPP\Student\Exceptions\NonExisting_Exception;
class Move {

  use InstrTrait;
  use SymbTrait;
  private mixed $final_symb;
  private string $final_type;

  public function execute(Variable $var, Variable|string $symb, Frames $frames, string $type) : void{

    if (is_object($symb)) {
      list($this->final_symb, $this->final_type) = $this->parse_symb($symb, $frames, "Move");
    } else {
      $this->final_symb = $symb;
      $this->final_type = $type;
    }

    $frame = $var->frame;
    $frame = $var->checkFrames($frame, $frames); 
    
    if (!$var->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Move.");
    }

    $frame->setVariable($var->name, $this->final_symb, $this->final_type);

  }

}
