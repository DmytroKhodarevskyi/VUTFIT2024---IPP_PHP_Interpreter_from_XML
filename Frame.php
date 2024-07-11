<?php

namespace IPP\Student;


class Frame {

  /** @var array<bool|int|string|null> */
  private $frame;
  public string $name;

  /** @var array<string|null> */
  private $types = [];

  /** @var bool[] */
  private $init = [];

  public function __construct(string $name) {
    $this->frame = [];
    
    $this->name = $name;
  }

  public function setName(string $name) : void {
    $this->name = $name;
  }

  // debug function to print the frame
  public function writeFrame() : void {
    if (empty($this->frame)) {
        fwrite(STDERR, "\nr-----------------\n");
        fwrite(STDERR, "Frame is empty\n");
        fwrite(STDERR, "L-----------------\n\n");
        return;
      }
      
    fwrite(STDERR, "\nr-----------------\n");
    fwrite(STDERR, "Frame: " . $this->name . "\n");
    foreach ($this->frame as $key => $value) {
      // Check if the value is not scalar (e.g., array, object), handle it appropriately
      if (is_scalar($value)) {
        // Scalar values (int, float, string, bool) can be printed directly
        fwrite(STDERR, $key . ": " . $value . "\n");
      }
    } 
    foreach ($this->types as $key => $value) {
      fwrite(STDERR, $key . ": " . $value . "\n");
    }
    fwrite(STDERR, "L-----------------\n\n");
}

  public function setVariable(null|string $variable, string|int|null|bool $value, null|string $type) : void {
    if ($type === 'bool') {
      if ($value === 'true' || $value === true || $value === 1) {
        $value = true;
      } else {
        $value = false;
      }
    }

    // If the variable was previously uninitialized, mark it as initialized
    if ($type !== null && $variable !== null) {
      if (!array_key_exists($variable, $this->init)) {
        $this->init[$variable] = true;
      }
    }

    $this->frame[$variable] = $value;

    // If the type is null, remove the type from the types array
    if (is_null($value)) {
        unset($this->types[$variable]);
    } else {
        $this->types[$variable] = $type;
        
    }
  }

  public function isInitialized(string $variable) : bool {
    if (array_key_exists($variable, $this->init)) {
      return true;
    } else {
      return false;
    }
  }

  public function getVariable(string $variable) : string|null|bool|int {

    if (array_key_exists($variable, $this->frame)) {
      $type = $this->getVariableType($variable);
      if ($type === 'int' && is_numeric($this->frame[$variable])) {
        return intval($this->frame[$variable]);
      }

      if ($type === 'bool' && is_string($this->frame[$variable])) {
        if ($this->frame[$variable] == 'true') {
          return true;
        } else {
          return false;
        }
      }

      return $this->frame[$variable];
    } else {
      return null;
    }
  }

  public function getVariableType(string $variable) : string|null {
    if (array_key_exists($variable, $this->types)) {

      if ($this->types[$variable] === 'NULL') {
        return "nil";
      }

      if ($this->types[$variable] === 'boolean') {
        return 'bool';
      }

      if ($this->types[$variable] === 'integer') {
        return 'int';
      }

      return $this->types[$variable];
    } else {
      return "nil";
    }
  }

  public function variableExists(string $variable) : bool {
    return array_key_exists($variable, $this->frame);
  }

}

?>