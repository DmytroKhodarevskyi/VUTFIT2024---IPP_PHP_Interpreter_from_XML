<?php

namespace IPP\Student;
use IPP\Student\Exceptions\NonExisting_Exception;
class Read {
  use InstrTrait;

  public function execute(Variable $var, string $type, Frames $frames, mixed $input) : void {
    $var_name = $var->name;
    $var_type = $type;

    $frame_working_with = $var->checkFrames($var->frame, $frames);

    $is_err_type = false;

    $input_type = $this->check_type($input);
    if (!$this->check_type_compatible($input_type, $var_type) || $input_type == "nil") {
      $input = null;
      $is_err_type = true;
    }

    if (!$is_err_type) {
      if ($input_type == "nil") {
        $input = null;
      } else if ($input_type == "int" && is_numeric($input)) {
        $input = intval($input);
      } else if ($input_type == "bool") {
        if ($input == "true") {
          $input = true;
        } else if ($input == "false") {
          $input = false;
        } else {
          $input = null;
        }
      } 
    }

    if ($frame_working_with->variableExists($var_name)) {
      $frame_working_with->setVariable($var_name, $input, $input_type);
    } else {
      throw new NonExisting_Exception("Non existing variable in Read instruction.");
    }

  }

  private function check_type(mixed $input) : string {

    if (is_numeric($input)) {
      // Simple way to convert to int or float based on content
      $input = $input + 0;
    }

    $real_type = gettype($input);

    if ($real_type == "integer") {
      return "int";
    } else if ($real_type == "boolean") {
      return "bool";
    } else if ($real_type == "string") {
      return "string";
    } else {
      return "nil";
    }
  }

  private function check_type_compatible(string $input_type, string $argument_type) : bool {
    if ($input_type == "nil" && $argument_type == "nil") {
      return true;
    } else if ($input_type == "int" && $argument_type == "int") {
      return true;
    } else if ($input_type == "bool" && $argument_type == "bool") {
      return true;
    } else if ($input_type == "string" && $argument_type == "string") {
      return true;
    } else {
      return false;
    }
  }
}