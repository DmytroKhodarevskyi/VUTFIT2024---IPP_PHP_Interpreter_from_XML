<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\NonExisting_Exception;

class Jumpifeq {

  use InstrTrait;
  use SymbTrait;
  private mixed $final_symb1;
  private string $final_type1;
  private mixed $final_symb2;
  private string $final_type2;

  public function execute(Variable|string $symb1, Variable|string $symb2, Frames $frames, string $type1, string $type2) : bool {

    $this->final_symb1 = $symb1;
    $this->final_type1 = $type1;
    $this->final_symb2 = $symb2;
    $this->final_type2 = $type2;

    if (is_object($symb1)) {
      list($this->final_symb1, $this->final_type1) = $this->parse_symb($symb1, $frames, "Jumpifeq");
    } 

    if (is_object($symb2)) {
      list($this->final_symb2, $this->final_type2) = $this->parse_symb($symb2, $frames, "Jumpifeq");
    }

    if ($this->final_type1 != "nil" && $this->final_type2 != "nil") {
      if ($this->final_type1 != $this->final_type2) {
        throw new BadType_Exception("Bad type in Jumpifeq.");
      }
    }

    if ($this->final_symb1 == $this->final_symb2) {
      return true;
    } else {
      return false;
    }
    
  }

}
