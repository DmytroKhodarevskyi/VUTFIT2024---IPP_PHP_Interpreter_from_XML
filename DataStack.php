<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadOperation_Exception;
use IPP\Student\Exceptions\BadString_Exception;
use IPP\Student\Exceptions\BadType_Exception;
use IPP\Student\Exceptions\BadValue_Exception;

class DataStack {

  use InstrTrait;

  /** @var string[] */
  private $data_stack = [];
  /** @var string[] */
  private $data_stack_types = [];

  public function pushs(string|int $data, string $type) : void {
    array_push($this->data_stack, $data);
    array_push($this->data_stack_types, $type);
  }

  /**
 * @return array<string|null>
  */
  public function pops() : array {
    if (empty($this->data_stack)) {
      throw new BadValue_Exception("DataStack is empty for pops() method.");
    }

    $data = array_pop($this->data_stack);

    $type = array_pop($this->data_stack_types);
    return [$data, $type];
  }

  public function adds() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if (gettype($data1) == "string" && $data1type == "int") {
      $data1 = intval($data1);
    }

    if (gettype($data2) == "string" && $data2type == "int") {
      $data2 = intval($data2);
    }

    if (is_int($data1) && is_int($data2)) {
      $result = $data1 + $data2;
      $this->pushs($result, "int");
    } else {
      throw new BadType_Exception("Bad type in DataStack adds() method.");
    }
  }

  public function subs() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if (gettype($data1) == "string" && $data1type == "int") {
      $data1 = intval($data1);
    }

    if (gettype($data2) == "string" && $data2type == "int") {
      $data2 = intval($data2);
    }

    if (is_int($data1) && is_int($data2)) {
      $result = $data2 - $data1;
      $this->pushs($result, "int");
    } else {
      throw new BadType_Exception("Bad type in DataStack subs() method.");
    }
  }

  public function muls() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if (gettype($data1) == "string" && $data1type == "int") {
      $data1 = intval($data1);
    }

    if (gettype($data2) == "string" && $data2type == "int") {
      $data2 = intval($data2);
    }

    if (is_int($data1) && is_int($data2)) {
      $result = $data1 * $data2;
      $this->pushs($result, "int");
    } else {
      throw new BadType_Exception("Bad type in DataStack muls() method.");
    }
  }

  public function idivs() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if (gettype($data1) == "string" && $data1type == "int") {
      $data1 = intval($data1);
    }

    if (gettype($data2) == "string" && $data2type == "int") {
      $data2 = intval($data2);
    }
    
    if (is_int($data1) && is_int($data2)) {
      if ($data2 == 0) {
        throw new BadValue_Exception("Bad value in DataStack idivs() method.");
      }
      $result = $data2 / $data1;
      $this->pushs(intval($result), "int");
    } else {
      throw new BadType_Exception("Bad type in DataStack idivs() method.");
    }
  }

  public function clears() : void {
    $this->data_stack = [];
    $this->data_stack_types = [];
  }

  public function lts() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if (gettype($data1) == "string" && $data1type == "int") {
      $data1 = intval($data1);
    }

    if (gettype($data2) == "string" && $data2type == "int") {
      $data2 = intval($data2);
    }
    
    if (gettype($data1) == "string" && $data1type == "bool") {
      $data1 = $data1 == "true" ? true : false;
    }

    if (gettype($data2) == "string" && $data2type == "bool") {
      $data2 = $data2 == "true" ? true : false;
    }

    if (is_int($data1) && is_int($data2)) {
      $result = $data2 < $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    }

    if (is_string($data1) && is_string($data2)) {
      $result = $data2 < $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    }

    if (is_bool($data1) && is_bool($data2)) {
      $result = $data2 < $data1 ? "true" : "false";
      $this->pushs($result, "bool");
    } else {
      throw new BadType_Exception("Bad type in DataStack lts() method.");
    }
  }

  public function gts() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if (gettype($data1) == "string" && $data1type == "int") {
      $data1 = intval($data1);
    }

    if (gettype($data2) == "string" && $data2type == "int") {
      $data2 = intval($data2);
    }

    if (gettype($data1) == "string" && $data1type == "bool") {
      $data1 = $data1 == "true" ? true : false;
    }

    if (gettype($data2) == "string" && $data2type == "bool") {
      $data2 = $data2 == "true" ? true : false;
    }

    if (is_int($data1) && is_int($data2)) {
      $result = $data2 > $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    } 

    if (is_string($data1) && is_string($data2)) {
      $result = $data2 > $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    } 

    if (is_bool($data1) && is_bool($data2)) {
      $result = $data2 > $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    } else {
      throw new BadType_Exception("Bad type in DataStack gts() method.");
    }
  }

  public function eqs() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if (gettype($data1) == "string" && $data1type == "int") {
      $data1 = intval($data1);
    }

    if (gettype($data2) == "string" && $data2type == "int") {
      $data2 = intval($data2);
    }

    if (gettype($data1) == "string" && $data1type == "bool") {
      $data1 = $data1 == "true" ? true : false;
    }

    if (gettype($data2) == "string" && $data2type == "bool") {
      $data2 = $data2 == "true" ? true : false;
    }

    if ($data1type == "nil" && $data2type == "nil") {
      $this->pushs("true", "bool");
      return;
    }

    if ($data1type == "nil" || $data2type == "nil") {
      $this->pushs("false", "bool");
      return;
    }

    if (is_int($data1) && is_int($data2)) {
      $result = $data2 == $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    } 
    
    if (is_string($data1) && is_string($data2)) {
      $result = $data2 == $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    } 
    
    if (is_bool($data1) && is_bool($data2)) {
      $result = $data2 == $data1 ? "true" : "false";
      $this->pushs($result, "bool");
      return;
    }

    throw new BadType_Exception("Bad type in DataStack eqs() method.");
  }

  public function ands() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if ($data1type == "bool" && $data2type == "bool") {
      if ($data1 == "true" && $data2 == "true") {
        $result = "true";
      } else {
        $result = "false";
      }
    } else {
      throw new BadType_Exception("Bad type in DataStack ands() method.");
    }


    $this->pushs($result, "bool");
  }

  public function ors() : void {
    list($data1, $data1type) = $this->pops();
    list($data2, $data2type) = $this->pops();

    if ($data1type == "bool" && $data2type == "bool") {
      if ($data1 == "true" || $data2 == "true") {
        $result = "true";
      } else {
        $result = "false";
      }
    } else {
      throw new BadType_Exception("Bad type in DataStack ors() method.");
    }

    $this->pushs($result, "bool");
  }

  public function nots() : void {
    list($data1, $data1type) = $this->pops();

    if ($data1type == "bool") {
      if ($data1 == "true") {
        $result = "false";
      } else {
        $result = "true";
      }
    } else {
      throw new BadType_Exception("Bad type in DataStack nots() method.");
    }

    $this->pushs($result, "bool");
  }

  public function int2chars() : void {
    list($data1, $data1type) = $this->pops();

    if ($data1type == "int") {
      $data1 = intval($data1);
      if ($data1 < 0 || $data1 > 255) {
        throw new BadString_Exception("Bad value in DataStack int2chars() method.");
      }
      $result = mb_chr($data1);
      $this->pushs($result, "string");
    } else {
      throw new BadType_Exception("Bad type in DataStack int2chars() method.");
    }
  }

  public function stri2ints() : void {
    list($data2, $data2type) = $this->pops();
    list($data1, $data1type) = $this->pops();

    if ($data1type != "string") {
      throw new BadType_Exception("Bad type in DataStack stri2ints() method.");
    }

    if ($data2type != "int") {
      throw new BadType_Exception("Bad type in DataStack stri2ints() method.");
    }

    $data2 = intval($data2);

    $result = null;

    if ($data1 !== null) {
      if ($data2 < 0 || $data2 >= mb_strlen($data1)) {
        throw new BadString_Exception("Bad value in DataStack stri2ints() method.");
      }

      $result = mb_ord($data1[$data2]);

    $this->pushs($result, "int");
    }
  }

  public function jumpifeqs() : bool {
    $this->eqs();
    list($data1, $data1type) = $this->pops();

    if ($data1type != "bool") {
      throw new BadType_Exception("Bad type in DataStack jumpifneqs() method.");
    }

    if ($data1 == "true") {
      return true;
    } else {
      return false;
    }
  }

  public function PrintStack() : void {
    fprintf(STDERR, "Data stack: ");
    foreach ($this->data_stack as $data) {
      fprintf(STDERR, $data . " ");
    }
    fprintf(STDERR, "\n");
  }

}
