<?php

namespace IPP\Student;
use IPP\Student\Exceptions\BadFrame_Exception;
class Frames {

  private Frame|null $temporary_frame = null;

  private bool $temporary_frame_active;
  
  private Frame $global_frame;

  /** @var Frame[] */
  private $local_frames;

  public function __construct() {
    $this->temporary_frame_active = false;
    $this->local_frames = [];
    $this->global_frame = new Frame('GF');
  }

  public function pushframe() : void {
    if ($this->temporary_frame == null) {
      throw new BadFrame_Exception("Bad frame in pushframe() method.");
    }
    $newframe = $this->temporary_frame;
    if ($newframe == null) {
      throw new BadFrame_Exception("Bad frame in pushframe() method.");
    }
    $newframe->setName('LF');
    array_push($this->local_frames, $newframe);

    $this->temporary_frame = null;
    $this->temporary_frame_active = false;
  }

  public function popframe() : void {
    $this->temporary_frame = array_pop($this->local_frames);
    if ($this->temporary_frame == null) {
      throw new BadFrame_Exception("Bad frame in popframe() method.");
    }
    if ($this->temporary_frame !== null) {
      $this->temporary_frame_active = true;
      $this->temporary_frame->setName('TF');
    }
  }

  public function isActiveTemporaryFrame() : bool {
    return $this->temporary_frame_active;
  }

  public function createframe() : void {
    $this->temporary_frame = new Frame('TF');
    $this->temporary_frame_active = true;
  }

  public function getGlobalFrame() : Frame {
    return $this->global_frame;
  }

  public function getTemporaryFrame() : ?Frame {
    return $this->temporary_frame;
  }

  public function getLocalFrame() : ?Frame {
    if (empty($this->local_frames)) {
      return null;
    }
    return end($this->local_frames);
  }

  public function LocalExists() : bool {
    return !empty($this->local_frames);
  }

}

?>