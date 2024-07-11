<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadOperation_Exception;

class Relative {

  use InstrTrait;
  use SymbTrait;

  private mixed $final_symb2;
  private string $final_type2;
  private mixed $final_symb3;
  private string $final_type3;

  public function execute(Variable $var1, Variable|string $symb2, Variable|string $symb3, Frames $frames, string $type2, string $type3, string $instr_name) : void {

    if (is_object($symb2)) {
      list($this->final_symb2, $this->final_type2) = $this->parse_symb($symb2, $frames, "Relative");
    } else {
      $this->final_symb2 = $symb2;
      $this->final_type2 = $type2;
    }

    if (is_object($symb3)) {
      list($this->final_symb3, $this->final_type3) = $this->parse_symb($symb3, $frames, "Relative");
    } else {
      $this->final_symb3 = $symb3;
      $this->final_type3 = $type3;
    }

    $name = $var1->name;
    $frame = $var1->frame;

    $frame = $var1->checkFrames($frame, $frames);
    if (!$var1->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Concat instruction.");
    }

    $result = false;

    if ($instr_name == 'LT' || $instr_name == 'GT') {
      if ($this->final_type2 == 'nil' || $this->final_type3 == 'nil') {
        throw new BadType_Exception("Bad type in Arithmetic instruction. Can't compare nil on GT or LT.");
      }
      if ($this->final_type2 != $this->final_type3) {
        throw new BadType_Exception("Bad type in Arithmetic instruction. Can't compare different types.");
      }
    }

    if ($instr_name == 'EQ') {
      if ($this->final_type2 == 'nil' && $this->final_type3 == 'nil') {
        $result = true;
        $frame->setVariable($name, $result, "bool");
        return;
      }
      if ($this->final_type2 == 'nil' || $this->final_type3 == 'nil') {
        $result = false;
        $frame->setVariable($name, $result, "bool");
        return;
      }
      if ($this->final_type2 != $this->final_type3) {
        throw new BadType_Exception("Bad type in Arithmetic instruction. Can't compare different types.");
      }
    }

    if ($instr_name == "LT") {
      $frame->setVariable($name, $this->final_symb2 < $this->final_symb3, "bool");
    }
    if ($instr_name == "GT") {
      $frame->setVariable($name, $this->final_symb2 > $this->final_symb3, "bool");
    }
    if ($instr_name == "EQ") {
      $frame->setVariable($name, $this->final_symb2 == $this->final_symb3, "bool");
    }
  }

}
