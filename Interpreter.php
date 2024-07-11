<?php

namespace IPP\Student;

use IPP\Student\Exceptions\BadValue_Exception;
use IPP\Student\Exceptions\SemanticCtrl_Exception;
use IPP\Student\Exceptions\Success_Exception;
use IPP\Student\Exceptions\NonExisting_Exception;
use IPP\Student\Exceptions\BadFrame_Exception;
use IPP\Student\Exceptions\BadType_Exception;

use IPP\Core\AbstractInterpreter;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {

        $dom = $this->source->getDOMDocument();
        $validate = new ValidateXML();
        $validate->validateXML($dom);
        $xml2code = new XML_to_code();
        $commands_array = $xml2code->parse_instructions($dom);
        $labels_array = [];
        $return_array = [];
        // $xml2code->print_commands_array();

        $Frames = new Frames();
        $DataStack = new DataStack();

        // fill labels array
        for ($i = 0; $i < count($commands_array); $i++) {
            $instr = $commands_array[$i];

            if ($instr->opcode == 'LABEL') {
                $label_name = $instr->args[0];
                if (array_key_exists($label_name, $labels_array)) {
                    $index = $labels_array[$label_name];
                    throw new SemanticCtrl_Exception("Label " . $label_name . " already exists on instruction " . $index . "\n");
                }

                $labels_array[$label_name] = $i;
            }
        }

        // execute instructions
        for ($i = 0; $i < count($commands_array); $i++) {
            $instr = $commands_array[$i];

            if ($instr->opcode == 'DEFVAR') {
                $defvar = new Defvar();
                $var_string = $instr->args[0];
                $var = $defvar->parse_argument($var_string);
                $frame = $var->checkFrames($var->frame, $Frames);

                $defvar->execute($var->name, $frame);
            }

            if ($instr->opcode == 'READ') {
                $read = new Read();

                $var = $read->parse_argument($instr->args[0]);
                $type = $instr->args[1];
                $input = null;  

                if ($type == 'int') {
                    $input = $this->input->readInt();
                }
                else if ($type == 'bool') {
                    $input = $this->input->readBool();
                }
                else if ($type == 'string') {
                    $input = $this->input->readString();
                }

                $read->execute($var, $type, $Frames, $input);
                
            }

            if ($instr->opcode == 'WRITE') {
                $write = new Write();
                $type = $instr->types[0];
                $var = $write->parse_argument($instr->args[0], $instr->types[0]);

                if (is_object($var)) {
                    $frame = $var->frame;
                    $value = null;
                    if ($frame == 'GF') {
                        if ($Frames->getGlobalFrame()->variableExists($var->name)) {
                                $value = $Frames->getGlobalFrame()->getVariable($var->name);
                                $type = $Frames->getGlobalFrame()->getVariableType($var->name);
                            if (($type == 'integer' || $type == 'int') && gettype($value) == 'integer'){
                                $this->stdout->writeInt($value);
                            }
                            else if (($type == 'boolean' || $type == 'bool') && gettype($value) == 'boolean') {
                                $this->stdout->writeBool($value);
                            }
                            else if ($type == 'string' && gettype($value) == 'string'){
                                $value = StringTransformer::transform($value);
                                $this->stdout->writeString($value);
                            } else if ($type == 'NULL' || $type == 'nil') {
                                $this->stdout->writeString("");
                            }
                        } else {
                            throw new NonExisting_Exception("Variable does not exist in Global Frame.");
                        }
                    } else if ($frame == 'LF') {
                        $localFrame = null;
                        if ($Frames->LocalExists()) {
                            $localFrame = $Frames->getLocalFrame();
                        } else {
                            throw new BadFrame_Exception("Local Frame does not exist.");
                        } 
                            if ($localFrame !== null) {
                                if ($localFrame->variableExists($var->name)) {
                                $value = $localFrame->getVariable($var->name);
                                $type = $localFrame->getVariableType($var->name);

                                if (($type == 'integer' || $type == 'int') && gettype($value) == 'integer'){
                                    $this->stdout->writeInt($value);
                                }
                                else if (($type == 'boolean' || $type == 'bool') && gettype($value) == 'boolean'){
                                    $this->stdout->writeBool($value);
                                }
                                else if (($type == 'string') && gettype($value) == 'string') {
                                    $this->stdout->writeString($value);
                                } else if ($type == 'NULL' || $type == 'nil') {
                                    $this->stdout->writeString("");
                                }
                            } else {
                                throw new NonExisting_Exception("Variable does not exist in Local Frame.");
                            }
                        }
                    } else if ($frame == 'TF') {
                        if (!$Frames->isActiveTemporaryFrame() || $Frames->getTemporaryFrame() === null) {
                            throw new BadFrame_Exception("Temporary Frame does not exist.");
                        }
                        if ($Frames->getTemporaryFrame()->variableExists($var->name)) {
                            $value = $Frames->getTemporaryFrame()->getVariable($var->name);
                            $type = $Frames->getTemporaryFrame()->getVariableType($var->name);
                            if (($type == 'integer' || $type == 'int') && gettype($value) == 'integer') {
                                $this->stdout->writeInt($value);
                            }
                            else if (($type == 'boolean' || $type == 'bool') && gettype($value) == 'boolean') {
                                $this->stdout->writeBool($value);
                            }
                            else if (($type == 'string') && gettype($value) == 'string') {
                                $this->stdout->writeString($value);
                            } else if ($type == 'NULL' || $type == 'nil') {
                                $this->stdout->writeString("");
                            }
                        } else {
                            throw new NonExisting_Exception("Variable does not exist in Temporary Frame.");
                        }
                    }
                }

                else if (is_string($var)) {
                    $this->stdout->writeString("$var");
                }

                else if (is_int($var)) {
                    $this->stdout->writeInt($var);
                }

                else if (is_bool($var)) {
                    $this->stdout->writeBool($var);
                }

                else if (is_null($var)) {
                    $this->stdout->writeString("");
                }

            }

            if ($instr->opcode == 'GETCHAR') {
                $getchar = new Getchar();
                $var_insert_to = $getchar->parse_argument($instr->args[0]);
                $symb_get_from = null;
                $symb_pos = null;

                if ($instr->types[1] == 'var') {
                    $symb_get_from = $getchar->parse_argument($instr->args[1]);
                } else {
                    if ($instr->types[1] !== 'string') {
                        throw new BadType_Exception("Bad type in GETCHAR (string expected).");
                    }
                    $symb_get_from = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb_pos = $getchar->parse_argument($instr->args[2]);
                } else {
                    if ($instr->types[2] !== 'int') {
                        throw new BadType_Exception("Bad type in GETCHAR (int expected).");
                    }
                    $symb_pos = $instr->args[2];
                }

                $getchar->execute($var_insert_to, $symb_get_from, $symb_pos, $Frames);
            }

            if ($instr->opcode == 'JUMPIFEQ') {
                $jumpifeq = new Jumpifeq();
                $label_name = $instr->args[0];

                if (!array_key_exists($label_name, $labels_array)) {
                    throw new SemanticCtrl_Exception("Label " . $label_name . " does not exist.");
                }

                $type1 = $instr->types[1];
                $type2 = $instr->types[2];

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $jumpifeq->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $jumpifeq->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                if ($jumpifeq->execute($symb1, $symb2, $Frames, $type1, $type2)) {
                    $i = $labels_array[$label_name];
                }
            }

            if ($instr->opcode == 'JUMPIFNEQ') {
                $jumpifeq = new Jumpifeq();
                $label_name = $instr->args[0];

                if (!array_key_exists($label_name, $labels_array)) {
                    throw new SemanticCtrl_Exception("Label " . $label_name . " does not exist.");
                }

                $type1 = $instr->types[1];
                $type2 = $instr->types[2];

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $jumpifeq->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $jumpifeq->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                if (!$jumpifeq->execute($symb1, $symb2, $Frames, $type1, $type2)) {
                    $i = $labels_array[$label_name];
                }
            }

            if ($instr->opcode == 'CONCAT') {
                $concat = new Concat();
                $var1 = $instr->args[0];
                $var1 = $concat->parse_argument($var1);

                $symb2 = null;
                $symb3 = null;

                if ($instr->types[1] == 'var') {
                    $symb2 = $concat->parse_argument($instr->args[1]);
                } else {
                    $symb2 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb3 = $concat->parse_argument($instr->args[2]);
                } else {
                    $symb3 = $instr->args[2];
                }


                $concat->execute($var1, $symb2, $symb3, $Frames, $instr->types[1], $instr->types[2]);
            }

            if ($instr->opcode == 'JUMP') {
                $label_name = $instr->args[0];
                if (!array_key_exists($label_name, $labels_array)) {
                    throw new SemanticCtrl_Exception("Label " . $label_name . " does not exist.");
                }

                $i = $labels_array[$label_name];
            }

            if ($instr->opcode == 'PUSHS') {
                $symb = null;
                $type = $instr->types[0];

                if ($instr->types[0] == 'var') {
                    $symb = $DataStack->parse_argument($instr->args[0]);
                } else {
                    $symb = $instr->args[0];
                }

                if ($symb !== null) {
                    if (!is_object($symb)) {
                        $DataStack->pushs($symb, $type);
                        continue;
                    }
                    /** @var Variable $symb */
                    if (is_object($symb)) {
                        $frame = $symb->frame;
                        $name = $symb->name;
                        $frame = $symb->checkFrames($frame, $Frames);
                        if (!$symb->do_i_exist($frame->name, $Frames)) {
                            throw new NonExisting_Exception("Variable does not exist in PUSHS instruction.");
                        }
                        $value = $frame->getVariable($name);
                        $type = $frame->getVariableType($name);
                        $DataStack->pushs($value, $type);
                    } 
                }
            }

            if ($instr->opcode == 'POPS') {
                $var = $instr->args[0];

                $var = $DataStack->parse_argument($var);
                $frame = $var->checkFrames($var->frame, $Frames);
                if (!$var->do_i_exist($frame->name, $Frames)) {
                    throw new NonExisting_Exception("Variable does not exist in POPS instruction.");
                }

                $popped_value = null;
                $popped_type = null;
                list($popped_value, $popped_type) = $DataStack->pops();

                $frame->setVariable($var->name, $popped_value, $popped_type);

            }

            if ($instr->opcode == 'INT2CHAR') {
                $int2char = new Int2char();
                $var = $int2char->parse_argument($instr->args[0]);

                $symb = null;
                $symb_type = null;

                if ($instr->types[1] == 'var') {
                    $symb = $int2char->parse_argument($instr->args[1]);
                } else {
                    $symb = $instr->args[1];
                }

                $symb_type = $instr->types[1];

                $int2char->execute($var, $symb, $Frames, $symb_type);
            }

            if ($instr->opcode == 'MOVE') {
                $move = new Move();
                $var = $move->parse_argument($instr->args[0]);
                $symb = null;
                $symb_type = null;

                if ($instr->types[1] == 'var') {
                    $symb = $move->parse_argument($instr->args[1]);
                } else {
                    $symb = $instr->args[1];
                }

                $symb_type = $instr->types[1];

                $move->execute($var, $symb, $Frames, $symb_type);
            }

            if ($instr->opcode == 'ADD') {
                $add = new Arithmetic();
                $var = $add->parse_argument($instr->args[0]);

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $add->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $add->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                $add->execute($var, $symb1, $symb2, $Frames, $instr->types[1], $instr->types[2], "ADD");
            }

            if ($instr->opcode == 'SUB') {
                $sub = new Arithmetic();
                $var = $sub->parse_argument($instr->args[0]);

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $sub->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $sub->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                $sub->execute($var, $symb1, $symb2, $Frames, $instr->types[1], $instr->types[2], "SUB");
            }

            if ($instr->opcode == 'MUL') {
                $mul = new Arithmetic();
                $var = $mul->parse_argument($instr->args[0]);

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $mul->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $mul->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                $mul->execute($var, $symb1, $symb2, $Frames, $instr->types[1], $instr->types[2], "MUL");
            }

            if ($instr->opcode == 'IDIV') {
                $idiv = new Arithmetic();
                $var = $idiv->parse_argument($instr->args[0]);

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $idiv->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $idiv->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                $idiv->execute($var, $symb1, $symb2, $Frames, $instr->types[1], $instr->types[2], "IDIV");
            }

            if ($instr->opcode == 'AND') {
                $and = new Boolean();
                $var = $and->parse_argument($instr->args[0]);

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $and->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $and->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                $and->execute($var, $symb1, $symb2, $Frames, $instr->types[1], $instr->types[2], "AND");
            }

            if ($instr->opcode == 'OR') {
                $or = new Boolean();
                $var = $or->parse_argument($instr->args[0]);

                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $or->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $or->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                $or->execute($var, $symb1, $symb2, $Frames, $instr->types[1], $instr->types[2], "OR");
            }

            if ($instr->opcode == 'NOT') {
                $not = new Boolean();
                $var = $not->parse_argument($instr->args[0]);

                $symb1 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $not->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                $not->execute_not($var, $symb1, $Frames, $instr->types[1]);
            }

            if ($instr->opcode == 'CALL') {
                $label_name = $instr->args[0];
                if (!array_key_exists($label_name, $labels_array)) {
                    throw new SemanticCtrl_Exception("Label " . $label_name . " does not exist.");
                } else {
                    array_push($return_array, $i);
                    $i = $labels_array[$label_name];
                }
            }

            if ($instr->opcode == 'RETURN') {
                if (count($return_array) === 0) {
                    throw new BadValue_Exception("No return address.");
                } else {
                    $i = array_pop($return_array);
                }
            }

            if ($instr->opcode == 'CREATEFRAME') {
                $Frames->createframe();
            }

            if ($instr->opcode == 'PUSHFRAME') {
                $Frames->pushframe();
            }

            if ($instr->opcode == 'POPFRAME') {
                $Frames->popframe();
            }

            if ($instr->opcode == 'LT') {
                $lt = new Relative();
                $var1 = $lt->parse_argument($instr->args[0]);

                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb2 = $lt->parse_argument($instr->args[1]);
                } else {
                    $symb2 = $instr->args[1];
                }

                $symb3 = null;

                if ($instr->types[2] == 'var') {
                    $symb3 = $lt->parse_argument($instr->args[2]);
                } else {
                    $symb3 = $instr->args[2];
                }

                $lt->execute($var1, $symb2, $symb3, $Frames, $instr->types[1], $instr->types[2], "LT");
            }

            if ($instr->opcode == 'GT') {
                $gt = new Relative();
                $var1 = $gt->parse_argument($instr->args[0]);

                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb2 = $gt->parse_argument($instr->args[1]);
                } else {
                    $symb2 = $instr->args[1];
                }

                $symb3 = null;

                if ($instr->types[2] == 'var') {
                    $symb3 = $gt->parse_argument($instr->args[2]);
                } else {
                    $symb3 = $instr->args[2];
                }

                $gt->execute($var1, $symb2, $symb3, $Frames, $instr->types[1], $instr->types[2], "GT");
            }

            if ($instr->opcode == 'EQ') {
                $eq = new Relative();
                $var1 = $eq->parse_argument($instr->args[0]);

                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb2 = $eq->parse_argument($instr->args[1]);
                } else {
                    $symb2 = $instr->args[1];
                }

                $symb3 = null;

                if ($instr->types[2] == 'var') {
                    $symb3 = $eq->parse_argument($instr->args[2]);
                } else {
                    $symb3 = $instr->args[2];
                }

                $eq->execute($var1, $symb2, $symb3, $Frames, $instr->types[1], $instr->types[2], "EQ");
            }

            if ($instr->opcode == 'EXIT') {
                $exit = new _Exit();

                $symb = null;
                
                if ($instr->types[0] == 'var') {
                    $symb = $exit->parse_argument($instr->args[0]);
                } else {
                    $symb = $instr->args[0];
                }

                $exit->execute($symb, $instr->types[0], $Frames);
            }

            if ($instr->opcode == 'SETCHAR') {
                $setchar = new Setchar();
                $var = $setchar->parse_argument($instr->args[0]);
                $symb_pos = null;
                $symb_char = null;

                if ($instr->types[1] == 'var') {
                    $symb_pos = $setchar->parse_argument($instr->args[1]);
                } else {
                    $symb_pos = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb_char = $setchar->parse_argument($instr->args[2]);
                } else {
                    $symb_char = $instr->args[2];
                }

                $setchar->execute($var, $symb_pos, $symb_char, $Frames, $instr->types[1], $instr->types[2]);
            }

            if ($instr->opcode == 'STRLEN') {
                $strlen = new Strlen();
                $var = $strlen->parse_argument($instr->args[0]);
                $symb = null;

                if ($instr->types[1] == 'var') {
                    $symb = $strlen->parse_argument($instr->args[1]);
                } else {
                    $symb = $instr->args[1];
                }

                $strlen->execute($var, $symb, $Frames, $instr->types[1]);
            }

            if ($instr->opcode == 'TYPE') {
                $type = new Type();
                $var = $type->parse_argument($instr->args[0]);
                $symb = null;

                if ($instr->types[1] == 'var') {
                    $symb = $type->parse_argument($instr->args[1]);
                } else {
                    $symb = $instr->args[1];
                }

                $type->execute($var, $symb, $Frames, $instr->types[1]);
            }

            if ($instr->opcode == 'STRI2INT') {
                $stri2int = new Stri2int();
                $var = $stri2int->parse_argument($instr->args[0]);
                $symb1 = null;
                $symb2 = null;

                if ($instr->types[1] == 'var') {
                    $symb1 = $stri2int->parse_argument($instr->args[1]);
                } else {
                    $symb1 = $instr->args[1];
                }

                if ($instr->types[2] == 'var') {
                    $symb2 = $stri2int->parse_argument($instr->args[2]);
                } else {
                    $symb2 = $instr->args[2];
                }

                $stri2int->execute($var, $symb1, $symb2, $Frames, $instr->types[1], $instr->types[2]);
            }

            if ($instr->opcode == 'ADDS') {
                $DataStack->adds();
            }

            if ($instr->opcode == 'SUBS') {
                $DataStack->subs();
            }

            if ($instr->opcode == 'MULS') {
                $DataStack->muls();
            }

            if ($instr->opcode == 'IDIVS') {
                $DataStack->idivs();
            }

            if ($instr->opcode == 'LTS') {
                $DataStack->lts();
            }

            if ($instr->opcode == 'GTS') {
                $DataStack->gts();
            }

            if ($instr->opcode == 'EQS') {
                $DataStack->eqs();
            }

            if ($instr->opcode == 'ANDS') {
                $DataStack->ands();
            }

            if ($instr->opcode == 'ORS') {
                $DataStack->ors();
            }

            if ($instr->opcode == 'NOTS') {
                $DataStack->nots();
            }

            if ($instr->opcode == 'CLEARS') {
                $DataStack->clears();
            }

            if ($instr->opcode == 'INT2CHARS') {
                $DataStack->int2chars();
            }

            if ($instr->opcode == 'STRI2INTS') {
                $DataStack->stri2ints();
            }

            if ($instr->opcode == 'JUMPIFEQS') {
                $label_name = $instr->args[0];
                if ($DataStack->jumpifeqs()) {
                    $i = $labels_array[$label_name];
                }
            }

            if ($instr->opcode == 'JUMPIFNEQS') {
                $label_name = $instr->args[0];
                if (!$DataStack->jumpifeqs()) {
                    $i = $labels_array[$label_name];
                }
            }
        }

        throw new Success_Exception("Success");

    }
}
?>