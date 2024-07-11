<?php

namespace IPP\Student;

class Write {

  public function parse_argument(string $argument, string $type) : Variable|String|int|bool|null {

    $argument = ltrim($argument);

    if ($type == 'var') {
      $argument = explode('@', $argument);
      $variable = new Variable($argument[0], $argument[1], null);
      return $variable;
    }

    if ($type == 'string') {
      $decoded = html_entity_decode($argument);
      
      $transformed = StringTransformer::transform($decoded);

      return $transformed;
    }

    if ($type == 'int') {
      return (int) $argument;
    }

    if ($type == 'bool') {
      if ($argument == 'true') {
        return true;
      } else {
        return false;
      }
    }

    if ($type == 'nil') {
      return null;
    }

    return null;
  }

}