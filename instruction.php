<?php

namespace IPP\Student;

class instruction {
  public string $opcode;
  public int $order;

  public mixed $types = [];
  /** array<string> */
  public mixed $args = [];

  public function __construct(string $opcode, mixed $args, mixed $types, int $order) {
    $this->opcode = $opcode;
    $this->args = $args;
    $this->types = $types;
    $this->order = $order;
  }
}