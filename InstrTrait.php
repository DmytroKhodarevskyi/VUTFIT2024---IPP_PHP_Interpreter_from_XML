<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadType_Exception;

trait InstrTrait {
    public function parse_argument(string $argument) : Variable {
        $argument = trim($argument);
    
        // breaks the string into two parts
        $argument = explode('@', $argument);
        //creates a new variable
        $variable = new Variable($argument[0], $argument[1], null);
        if (!($variable->frame == "GF" || $variable->frame == "LF" || $variable->frame == "TF")) {
          throw new BadType_Exception("Bad frame in \"var\".");
        } else {
          return $variable;
        }
      }
}