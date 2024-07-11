<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadOperation_Exception;

class Arithmetic {

  use InstrTrait;
  use SymbTrait;

  private int|string $final_symb2;
  private string $final_type2;
  private int|string $final_symb3;
  private string $final_type3;

  public function execute(Variable $var1, Variable|string $symb2, Variable|string $symb3, Frames $frames, string $type2, string $type3, string $instr_name) : void {

    if (is_object($symb2)) {
      list($this->final_symb2, $this->final_type2) = $this->parse_symb($symb2, $frames, "Arithmetic");
    } else {
      $this->final_symb2 = $symb2;
      $this->final_type2 = $type2;
    }

    if (is_object($symb3)) {
      list($this->final_symb3, $this->final_type3) = $this->parse_symb($symb3, $frames, "Arithmetic");
    } else {
      $this->final_symb3 = $symb3;
      $this->final_type3 = $type3;
    }

    if ($this->final_type2 != "int" || $this->final_type3 != "int") {
      throw new BadType_Exception("Bad type in Arithmetic instruction.");
    }

    $name = $var1->name;
    $frame = $var1->frame;

    $frame = $var1->checkFrames($frame, $frames);
    if (!$var1->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Concat instruction.");
    }

    if ($instr_name == "ADD") {
      $frame->setVariable($name, intval($this->final_symb2) + intval($this->final_symb3), "int");
    }
    if ($instr_name == "SUB") {
      $frame->setVariable($name, intval($this->final_symb2) - intval($this->final_symb3), "int");
    }
    if ($instr_name == "MUL") {
      $frame->setVariable($name, intval($this->final_symb2) * intval($this->final_symb3), "int");
    }
    if ($instr_name == "IDIV") {
      if ($this->final_symb3 == 0) {
        throw new BadOperation_Exception("Division by zero in IDIV instruction.");
      }
      $frame->setVariable($name, intval($this->final_symb2) / intval($this->final_symb3), "int");
    }
  }

}
