<?php

namespace IPP\Student;
use IPP\Student\Exceptions\XmlSemanticErr_Exception;

class ValidateXML {
  private function checkForMisplacedArgs(mixed $node) : void {
    $validParentNames = ['instruction'];
    
    foreach ($node->childNodes as $child) {
      if (strpos($child->nodeName, 'arg') === 0) {
          if (!in_array($child->parentNode->nodeName, $validParentNames, true)) {
            throw new XmlSemanticErr_Exception("Invalid placement of {$child->nodeName} outside of instruction.");
          }
        }

        if ($child->hasChildNodes()) {
          $this->checkForMisplacedArgs($child);
        }

    }

  }

  private function checkInvalidTags(mixed $node) : void {
    $validTags = ['program', 'instruction', 'arg1', 'arg2', 'arg3'];

    if ($node->nodeType === XML_ELEMENT_NODE) {
      // Check if the tag is valid
      if (!in_array($node->nodeName, $validTags, true)) {
          throw new XmlSemanticErr_Exception("Invalid tag: {$node->nodeName}");
      }
    }

    foreach ($node->childNodes as $child) {
      // Recursively check child nodes
      $this->checkInvalidTags($child);
    }

  
  }

  public function validateXML(mixed $xml) : void {
    $this->checkInvalidTags($xml);
    $this->checkForMisplacedArgs($xml);
  }
}