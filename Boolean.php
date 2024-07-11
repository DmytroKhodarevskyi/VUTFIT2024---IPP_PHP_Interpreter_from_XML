<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadOperation_Exception;

class Boolean {

  use InstrTrait;
  use SymbTrait;

  private mixed $final_symb2;
  private string $final_type2;
  private mixed $final_symb3;
  private string $final_type3;

  public function execute(Variable $var1, Variable|string $symb2, Variable|string $symb3, Frames $frames, string $type2, string $type3, string $instr_name) : void {

    if (is_object($symb2)) {
      list($this->final_symb2, $this->final_type2) = $this->parse_symb($symb2, $frames, "Boolean");
    } else {
      $this->final_symb2 = $symb2;
      $this->final_type2 = $type2;
    }

    if (is_object($symb3)) {
      list($this->final_symb3, $this->final_type3) = $this->parse_symb($symb3, $frames, "Boolean");
    } else {
      $this->final_symb3 = $symb3;
      $this->final_type3 = $type3;
    }

    if ($this->final_type2 != "bool" || $this->final_type3 != "bool") {
      throw new BadType_Exception("Bad type in Boolean instruction.");
    }

    $name = $var1->name;
    $frame = $var1->frame;

    $frame = $var1->checkFrames($frame, $frames);
    if (!$var1->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Concat instruction.");
    }

    if ($this->final_symb2 == "true") {
      $this->final_symb2 = true;
    } else {
      $this->final_symb2 = false;
    }

    if ($this->final_symb3 == "true") {
      $this->final_symb3 = true;
    } else {
      $this->final_symb3 = false;
    }
    
    if ($instr_name == "AND") {
      $frame->setVariable($name, $this->final_symb2 and $this->final_symb3, "bool");
    }
    if ($instr_name == "OR") {
      $frame->setVariable($name, boolval($this->final_symb2 or $this->final_symb3), "bool");
    }

  }

  public function execute_not(Variable $var1, Variable|string $symb2, Frames $frames, string $type2) : void {

    if (is_object($symb2)) {
      list($this->final_symb2, $this->final_type2) = $this->parse_symb($symb2, $frames, "Boolean NOT");
    } else {
      if ($symb2 == "true") {
        $symb2 = true;
      } else {
        $symb2 = false;
      }
      $this->final_symb2 = $symb2;
      $this->final_type2 = $type2;
    }

    if ($this->final_type2 != "bool") {
      throw new BadType_Exception("Bad type in Boolean NOT instruction.");
    }

    $name = $var1->name;
    $frame = $var1->frame;

    $frame = $var1->checkFrames($frame, $frames);
    if (!$var1->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Concat instruction.");
    }

    if ($this->final_symb2 == true) {
      $this->final_symb2 = false;
    } else if ($this->final_symb2 == false) {
      $this->final_symb2 = true;
    }

    $frame->setVariable($name, $this->final_symb2, "bool");

  }

}
