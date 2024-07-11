<?php

namespace IPP\Student;

use IPP\Student\Exceptions\BadFrame_Exception;
use IPP\Student\Exceptions\SemanticCtrl_Exception;

class Variable {

  public string $frame;
  public string $name;
  public string|null $value;
  public function __construct(string $frame, string $name, string|null $value) {
    $this->frame = $frame;
    $this->name = $name;
    $this->value = $value;
  }

  // Check if the frame is valid and return the frame
  public function checkFrames(string $frame_search_in, Frames $frames) : Frame {
    if ($frame_search_in == "GF") {
      $frame_search_in = $frames->getGlobalFrame();
    } else if ($frame_search_in == "LF") {
      $frame_search_in = $frames->getLocalFrame();
      if ($frame_search_in == null) {
        throw new BadFrame_Exception("Local frame does not exist");
      }
    } else if ($frame_search_in == "TF") {
      if (!$frames->isActiveTemporaryFrame()) {
        throw new BadFrame_Exception("Temporary frame does not exist");
      }
      $frame_search_in = $frames->getTemporaryFrame();
    } else {
      throw new SemanticCtrl_Exception("Semantic error in Getchar instruction.");
    }

    return $frame_search_in;
  }

  // Check if the variable exists in the frame
  public function do_i_exist(string $frame, Frames $frames) : bool {

    if ($frame == "GF") {
      if ($frames->getGlobalFrame()->variableExists($this->name)) {
        return true;
      }
    } else if ($frame == "LF") {
      $lcf = $frames->getLocalFrame();
      if ($lcf !== null) {
        if ($lcf->variableExists($this->name)) {
          return true;
        }
      }
    } else if ($frame == "TF") {
      if ($frames->getTemporaryFrame()->variableExists($this->name)) {
        return true;
      }
    }

    return false;
  }

}
   