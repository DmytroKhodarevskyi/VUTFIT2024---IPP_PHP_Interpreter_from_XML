<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\BadOperation_Exception;

class _Exit {
  use SymbTrait;
  use InstrTrait;

  private mixed $final_symb;
  private mixed $final_type;

  public function execute(Variable|string $symb, string $type, Frames $frames) : void {

    if (is_object($symb)) {
      list($this->final_symb, $this->final_type) = $this->parse_symb($symb, $frames, "Exit");
    } else {
      $this->final_symb = $symb;
      $this->final_type = $type;
    }

    if ($this->final_type != "int") {
      throw new BadType_Exception("Bad type in Exit instruction.");
    }

    if ($this->final_symb < 0 || $this->final_symb > 9) {
      throw new BadOperation_Exception("Bad number in Exit instruction.");
    }

    $this->final_symb = intval($this->final_symb);

    exit($this->final_symb);
  }
}

