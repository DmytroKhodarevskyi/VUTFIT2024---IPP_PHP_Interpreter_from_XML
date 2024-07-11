<?php

namespace IPP\Student;
use IPP\Student\Exceptions\XmlSemanticErr_Exception;

class Check {

  public function check_type_string(string $type, string $string) : bool {
    if ($type == 'var') {
      return $this->check_variable($string);
    } else if ($type == 'int') {
      return $this->check_int($string);
    } else if ($type == 'bool') {
      return $this->check_bool($string);
    } else if ($type == 'string') {
      return $this->check_string($string);
    } else if ($type == 'nil') {
      return $this->check_nil($string);
    } else if ($type == 'type') {
      return $this->check_type($string);
    } else if ($type == 'label') {
      return $this->check_label($string);
    } else {
      throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type (probably wrong type name at all)");
    }
  }

  public function check_int(string $string) : bool {
    if (preg_match('/^[-+]?[0-9]+$/', $string)) {
      return true;
    } else {
      throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type (int)");
    }
  }

  public function check_bool(string $string) : bool {
    if (preg_match('/^(true|false)$/', $string)) {
      return true;
    } else {
      throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type (bool)");
    }
  }

  public function check_string(string $string) : bool {
    if (preg_match('/^.*/', $string)) {
      return true;
    } else {
      throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type (string)");
    }
  }

  public function check_nil(string $string) : bool {
    if (preg_match('/^nil$/', $string)) {
      return true;
    } else {
      throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type (nil)");
    }
  }

  public function check_type(string $string) : bool {
    $allowedTypes = ['int', 'bool', 'string', 'nil'];
    
    if (in_array($string, $allowedTypes)) {
        return true;
    } else {
        throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type (int/bool/string/nil)");
      }
}


  public function check_variable(string $string) : bool {

      if (preg_match('/^(GF|LF|TF)@.+/', $string)) {
        return true;
      } else {
        throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type (var)");
      }
  }

  public function check_label(string $string) : bool {
    if (preg_match('/^.+/', $string)) {
      return true;
    } else {
      throw new XmlSemanticErr_Exception("Wrong variable, not compatible with type");
    }
  }

}