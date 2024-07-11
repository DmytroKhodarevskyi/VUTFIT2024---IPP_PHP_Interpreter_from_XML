<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\NonExisting_Exception;

class Concat {

  use InstrTrait;
  use SymbTrait;

  private mixed $final_symb2;
  private string $final_type2;
  private mixed $final_symb3;
  private string $final_type3;

  public function execute(Variable $var1, Variable|string $symb2, Variable|string $symb3, Frames $frames, string $type2, string $type3) : void {

    if (is_object($symb2)) {
      list($this->final_symb2, $this->final_type2) = $this->parse_symb($symb2, $frames, "Concat");
    } else {
      $this->final_symb2 = $symb2;
      $this->final_type2 = $type2;
    }

    if (is_object($symb3)) {
      list($this->final_symb3, $this->final_type3) = $this->parse_symb($symb3, $frames, "Concat");
    } else {
      $this->final_symb3 = $symb3;
      $this->final_type3 = $type3;
    }

    if (!(
      $this->final_type2 == "string" && $this->final_type3 == "string"
      )) {
      throw new BadType_Exception("Bad type in Concat instruction.");
    }

    $name = $var1->name;
    $frame = $var1->frame;

    $frame = $var1->checkFrames($frame, $frames);
    if (!$var1->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Concat instruction.");
    }

    $frame->setVariable($name, $this->final_symb2 . $this->final_symb3, "string");

  }

}
