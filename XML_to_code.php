<?php

namespace IPP\Student;
use IPP\Student\Exceptions\XmlFormatErr_Exception;
use IPP\Student\Exceptions\XmlSemanticErr_Exception;

class XML_to_code {
  private mixed $dictionary = [];

  /** @var array<instruction> */
  private $commands_array;

  // Array of acceptable types for the symb argument
  const symb = ['int', 'bool', 'string', 'nil', 'var'];

  public function __construct() {
    $this->commands_array = [];
    $this->dictionary = [
      'READ' => ['var', 'type'],
      'MOVE' => ['var', self::symb],
      'CREATEFRAME' => [],
      'PUSHFRAME' => [],
      'POPFRAME' => [],
      'DEFVAR' => ['var'],
      'CALL' => ['label'],
      'RETURN' => [],
      'PUSHS' => [self::symb],
      'POPS' => ['var'],
      'ADD' => ['var', self::symb, self::symb],
      'ADDS' => [],
      'SUB' => ['var', self::symb, self::symb],
      'SUBS' => [],
      'MUL' => ['var', self::symb, self::symb],
      'MULS' => [],
      'IDIV' => ['var', self::symb, self::symb],
      'IDIVS' => [],
      'LT' => ['var', self::symb, self::symb],
      'LTS' => [],
      'GT' => ['var', self::symb, self::symb],
      'GTS' => [],
      'EQ' => ['var', self::symb, self::symb],
      'EQS' => [],
      'AND' => ['var', self::symb, self::symb],
      'ANDS' => [],
      'OR' => ['var', self::symb, self::symb],
      'ORS' => [],
      'NOT' => ['var', self::symb],
      'NOTS' => [],
      'INT2CHAR' => ['var', self::symb],
      'INT2CHARS' => [],
      'STRI2INT' => ['var', self::symb, self::symb],
      'STRI2INTS' => [],
      'CLEARS' => [],
      'WRITE' => [self::symb],
      'CONCAT' => ['var', self::symb, self::symb],
      'STRLEN' => ['var', self::symb],
      'GETCHAR' => ['var', self::symb, self::symb],
      'SETCHAR' => ['var', self::symb, self::symb],
      'TYPE' => ['var', self::symb],
      'LABEL' => ['label'],
      'JUMP' => ['label'],
      'JUMPIFEQ' => ['label', self::symb, self::symb],
      'JUMPIFEQS' => ['label'],
      'JUMPIFNEQ' => ['label', self::symb, self::symb],
      'JUMPIFNEQS' => ['label'],
      'DPRINT' => [self::symb],
      'BREAK' => [],
      'EXIT' => [self::symb],
    ];
  }

  /** @param string $opcode
   * @param array<string> $argTypes
   **/
  private function checkArgTypes(string $opcode, array $argTypes) : bool {
    if (!isset($this->dictionary[$opcode])) {
        throw new XmlSemanticErr_Exception("Opcode not found in the dictionary");
      }
      
      $expectedTypes = $this->dictionary[$opcode];
      
      if (count($argTypes) != count($expectedTypes)) {
        throw new XmlSemanticErr_Exception("Different number of arguments");
      }
      
      foreach ($argTypes as $index => $type) {
        // Check if the expected type at this position is an array of acceptable types
        if (is_array($expectedTypes[$index])) {
          // Check if the actual type is one of the acceptable types
          if (!in_array($type, $expectedTypes[$index])) {
            throw new XmlSemanticErr_Exception("Type mismatch");
          }
        } else {
          // Single expected type
          if ($type != $expectedTypes[$index]) {
              throw new XmlSemanticErr_Exception("Type mismatch");
            }
        }
    }

    return true; // All argument types match
}

  private function check_header(mixed $dom) : void {
    $program = $dom->getElementsByTagName('program')->item(0);
    $language = $program->getAttribute('language');
    if ($language != "IPPcode24") {
      throw new XmlSemanticErr_Exception("Invalid header");
    }
  }

  private function check_order(mixed $instructions) : void {
    $order_array = [];
    foreach ($instructions as $instruction) {
      $order = $instruction->getAttribute('order');

      if ($order <= 0) {
        fprintf(STDERR, "Invalid instruction order: $order\n");
        throw new XmlSemanticErr_Exception("Invalid instruction order");
      }

      array_push($order_array, $order);
    }

    $uniqueArray = array_unique($order_array);
    if (count($uniqueArray) != count($order_array)) {
      throw new XmlSemanticErr_Exception("Duplicates in instruction order");
    }
  }

  private function parse_instruction(mixed $instruction) : instruction {
    $opcode = strtoupper($instruction->getAttribute('opcode'));
    $order = $instruction->getAttribute('order');
    if ($order < 0 || !is_numeric($order)) {
      throw new XmlSemanticErr_Exception("Invalid instruction order");
    }

    if (!array_key_exists($opcode, $this->dictionary)) {
      throw new XmlSemanticErr_Exception("Invalid opcode: $opcode");
    }

    // Get the child nodes of the instruction element
    $childNodes = $instruction->childNodes;

    // Initialize a counter for child elements
    $numChildElements = 0;

    // Loop through each child node
    foreach ($childNodes as $node) {
        // Check if the node is an element node
        if ($node->nodeType === XML_ELEMENT_NODE) {
            // Increment the counter for element nodes
            $numChildElements++;
        }
    }

    if ($numChildElements != count($this->dictionary[$opcode])) {
      throw new XmlSemanticErr_Exception("Invalid number of arguments for opcode: $opcode");
    }

    // If there are no arguments
    if ($numChildElements == 0) {
      $args_array = [];
      $this->checkArgTypes($opcode, $args_array);
      $generated_instruction = new instruction($opcode, $args_array, [], $order);
      return $generated_instruction;
    }

    // If there is 1 argument
    if ($numChildElements == 1) {
      $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
      
      if ($arg1 == null) {
        throw new XmlSemanticErr_Exception("Missing arg1");
      }

      $type = trim($arg1->getAttribute('type'));

      $value = trim($arg1->nodeValue);

      $checker = new Check(); 

      $value = trim($value);

      $checker->check_type_string($type, $value);

      $types_array = [$type];
      $args_array = [$value];
      $this->checkArgTypes($opcode, $types_array);
      $generated_instruction = new instruction($opcode, $args_array, $types_array, $order);
      return $generated_instruction;
    }

    // If there are 2 arguments
    if ($numChildElements == 2) {
      $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
      $arg2 = $instruction->getElementsByTagName('arg2')->item(0);

      if ($arg1 == null) {
        throw new XmlSemanticErr_Exception("Missing arg1");
      }

      if ($arg2 == null) {
        throw new XmlSemanticErr_Exception("Missing arg2");
      }

      $type1 = trim($arg1->getAttribute('type'));
      $value1 = trim($arg1->nodeValue);
      $type2 = trim($arg2->getAttribute('type'));
      $value2 = trim($arg2->nodeValue);

      $checker = new Check(); 

      $value1 = trim($value1);
      $value2 = trim($value2);

      $checker->check_type_string($type1, $value1);
      $checker->check_type_string($type2, $value2);

      $types_array = [$type1, $type2];
      $args_array = [$value1, $value2];
      $this->checkArgTypes($opcode, $types_array);
      $generated_instruction = new instruction($opcode, $args_array, $types_array, $order);
      return $generated_instruction;
    }

    // If there are 3 arguments
    if ($numChildElements == 3) {

      $arg1 = $instruction->getElementsByTagName('arg1')->item(0);
      $arg2 = $instruction->getElementsByTagName('arg2')->item(0);
      $arg3 = $instruction->getElementsByTagName('arg3')->item(0);

      if ($arg1 == null) {
        throw new XmlSemanticErr_Exception("Missing arg1");
      }
      if ($arg2 == null) {
        throw new XmlSemanticErr_Exception("Missing arg2");
      }
      if ($arg3 == null) {
        throw new XmlSemanticErr_Exception("Missing arg3");
      }

      $type1 = trim($arg1->getAttribute('type'));
      $value1 = $arg1->nodeValue;
      $type2 = trim($arg2->getAttribute('type'));
      $value2 = $arg2->nodeValue;
      $type3 = trim($arg3->getAttribute('type'));
      $value3 = $arg3->nodeValue;

      $checker = new Check(); 

      $value1 = trim($value1);
      $value2 = trim($value2);
      $value3 = trim($value3);

      $checker->check_type_string($type1, $value1);
      $checker->check_type_string($type2, $value2);
      $checker->check_type_string($type3, $value3);

      $types_array = [$type1, $type2, $type3];
      $args_array = [$value1, $value2, $value3];
      $this->checkArgTypes($opcode, $types_array);
      $generated_instruction = new instruction($opcode, $args_array, $types_array, $order);
      return $generated_instruction;
    }

    throw new XmlFormatErr_Exception("Invalid number of arguments for opcode: $opcode");
  }

  private function compare_by_order(instruction $a, instruction $b) : int {
    if ($a->order == $b->order) {
        return 0;
    }
    return ($a->order < $b->order) ? -1 : 1;
  }

  /** @return array<instruction> */
  public function parse_instructions(mixed $dom) : array {

    $this->check_header($dom);

    $instructions = $dom->getElementsByTagName('instruction');

    $this->check_order($instructions);

    foreach ($instructions as $instruction) {
      $parsed_instruction = $this->parse_instruction($instruction);
      $this->commands_array[] = $parsed_instruction;
    }

    // Sort the commands array by order
    usort($this->commands_array, array($this, "compare_by_order"));

    return $this->commands_array;
  }

  // Debugging function to print the commands array
  public function print_commands_array() : void {
    foreach ($this->commands_array as $command) {
      echo $command->opcode . ": ";
      foreach ($command->args as $arg) {
        echo $arg . " ";
      }
      echo "\n";
      for ($i = 0; $i < strlen($command->opcode); $i++) {
        echo "-";
      }
      echo ": ";
      foreach ($command->types as $type) {
        echo "[". $type . "] ";
      }
      echo "\n";
    }
  }
}