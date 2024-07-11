<?php

namespace IPP\Student;
// require_once "Exceptions/BadType_Exception.php";
// require_once "Exceptions/NonExisting_Exception.php";
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadString_Exception;

class Int2char {

  use InstrTrait;

  private int $final_symb;
  private string $final_symb_type;

  public function execute(Variable $var1, Variable|int $symb1, Frames $frames, string $symb_type) : void {
      
      if (is_object($symb1)) {
        $this->final_symb = $this->parse_symb($symb1, $frames);
        $this->final_symb_type = "int";
      } else {
        $this->final_symb = $symb1;
        $this->final_symb_type = $symb_type;
      }

      if ($this->final_symb_type != "int") {
        throw new BadType_Exception("Bad type in Int2char.");
      }
  
      $name = $var1->name;
      
      $frame = $var1->frame;
      $frame = $var1->checkFrames($frame, $frames);
  
      if (!$var1->do_i_exist($frame->name, $frames)) {
        throw new NonExisting_Exception("Variable does not exist in Int2char.");
      }
  
      if ($this->final_symb < 0 || $this->final_symb > 0x10FFFF) {
        throw new BadString_Exception("Bad type in Int2char.");
      }

      $char = mb_chr($this->final_symb);
      $frame->setVariable($name, $char, "string");
  }

  private function parse_symb(Variable $symb, Frames $frames) : int|bool|string|null {

    $frame = $symb->frame;
    $name = $symb->name;

    $frame = $symb->checkFrames($frame, $frames);
    if (!$symb->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in Int2char.");
    }

    if ($frame->getVariableType($name) != "int") {
      throw new BadType_Exception("Bad type in Int2char.");
    }

    return $frame->getVariable($name);
  }
}

?>