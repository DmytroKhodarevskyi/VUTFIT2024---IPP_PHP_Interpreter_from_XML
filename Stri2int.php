<?php

namespace IPP\Student;

use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\BadString_Exception;
class Stri2int {

  use InstrTrait;
  use SymbTrait;
  private string|int $final_symb2;
  private string $final_type2;
  private string|int $final_symb3;
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

    if ($this->final_type3 != "int" || $this->final_type2 != "string") {
      throw new BadType_Exception("Bad type in Stri2int instruction.");
    }

    $frame = $var->frame;
    $frame = $var->checkFrames($frame, $frames); 
    
    if (!$var->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Stri2int.");
    }

    if ($this->final_symb3 < 0 || $this->final_symb3 > mb_strlen($this->final_symb2) - 1) {
      throw new BadString_Exception("Bad type in Stri2int instruction.");
    }

    $char = $this->final_symb2[intval($this->final_symb3)];
    $ordinal = mb_ord($char);
    
    $frame->setVariable($var->name, $ordinal, "int");
  }

  
}
