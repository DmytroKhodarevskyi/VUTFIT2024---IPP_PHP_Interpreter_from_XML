<?php

namespace IPP\Student;
use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadString_Exception;
use IPP\Student\Exceptions\BadType_Exception;

class Getchar {

  use InstrTrait;

  private string|bool|int|null $final_string = "";
  private int|bool|string $final_string_index = 0;

  public function execute(Variable $var_insert_to, string|Variable $symb_get_from, string|Variable $symb_pos, Frames $frames) : void {

    //variable
    if (is_object($symb_get_from)) {
      $this->parse_symb_get_from($symb_get_from, $frames);
    } else {
      $this->final_string = (string) $symb_get_from;
    }

    if (is_object($symb_pos)) {
      $this->parse_symb_pos($symb_pos, $frames);
    } else {
      $this->final_string_index = (int) $symb_pos;
    }

    $name = $var_insert_to->name;
    $frame = $var_insert_to->frame;

    $frame = $var_insert_to->checkFrames($frame, $frames);

    if (!$var_insert_to->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist (Getchar)");
    }

    if ($this->final_string_index < mb_strlen(strval($this->final_string)) && $this->final_string_index >= 0) {
      $char = $this->final_string[intval($this->final_string_index)];
      $frame->setVariable($name, $char, "string");
    } else {
      throw new BadString_Exception("Index out of bounds (Getchar)");
    }

  }

  // there is redundancy in the code, but I didn't have time to refactor it with traits
  // the traits was implemented after this function was written
  private function parse_symb_pos(Variable $symb_pos, Frames $frames) : void {
    $frame = $symb_pos->frame;
    $name = $symb_pos->name;

    $frame = $symb_pos->checkFrames($frame, $frames);
    if (!$symb_pos->do_i_exist($frame->name, $frames)) {
      throw new NonExisting_Exception("Variable does not exist (Getchar)");
    }

    $symb_pos = $frame->getVariable($name);
    $symb_pos_type = $frame->getVariableType($name);

    if ($symb_pos_type != "integer" && $symb_pos_type != "int") {
      throw new BadType_Exception("Variable is not of type int (Getchar)");
    }

    if ($symb_pos !== null) {
      $this->final_string_index = $symb_pos;
    }
  }

  // there is redundancy in the code, but I didn't have time to refactor it with traits
  // the traits was implemented after this function was written
  private function parse_symb_get_from(Variable $symb_get_from, Frames $frames) : void {
    $frame = $symb_get_from->frame;
      $name = $symb_get_from->name;

      $frame = $symb_get_from->checkFrames($frame, $frames);
      if (!$symb_get_from->do_i_exist($frame->name, $frames)) {
        throw new NonExisting_Exception("Variable does not exist (Getchar)");
      }

      $symb_get_from = $frame->getVariable($name);
      $symb_get_from_type = $frame->getVariableType($name);

      if ($symb_get_from_type != "string") {
        throw new BadType_Exception("Variable is not of type string (Getchar)");
      }

      $this->final_string = $symb_get_from;
  }

  
}