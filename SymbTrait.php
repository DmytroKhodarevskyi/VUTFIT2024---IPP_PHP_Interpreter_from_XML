<?php

namespace IPP\Student;
use IPP\Student\Exceptions\NonExisting_Exception;

trait SymbTrait {

  /**
   * Parse the symbol
   * 
   * @param Variable $symb
   * @param Frames $frames
   * @param string $instr_name
   * @return string[]
   */
  public function parse_symb(Variable $symb, Frames $frames, string $instr_name) : array {

    $frame = $symb->frame;
    $name = $symb->name;

    $frame = $symb->checkFrames($frame, $frames);
    if (!$symb->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist in" . $instr_name . " instruction.");
    }

    // return the variable and its type
    return [$frame->getVariable($name), $frame->getVariableType($name)];
  }
}