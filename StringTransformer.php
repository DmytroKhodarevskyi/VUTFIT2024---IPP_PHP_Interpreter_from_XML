<?php

namespace IPP\Student;

class StringTransformer {
  public static function transform(string $string) : string {
    $string = str_replace("\\n", "\n", $string); // New line
    $string = str_replace("\\t", "\t", $string); // Tab
    $string = str_replace("\\", "\\", $string); // Backslash
    $string = str_replace("\"", "\"", $string); // Double quote

    // Octal replacements
    $string = str_replace("\\040", " ", $string); // Space
    $string = str_replace("\\041", "!", $string); // Exclamation mark
    // Add more replacements as needed
    $string = str_replace("\\047", "'", $string); // Single quote (apostrophe)
    $string = str_replace("\\050", "(", $string); // Open parenthesis
    $string = str_replace("\\051", ")", $string); // Close parenthesis
    // Continue for other characters as required
    $string = str_replace("\\134", "\\", $string); // Backslash (redundant with above)
    $string = str_replace("\\042", "\"", $string); // Double quote (redundant with above)
    // The original octal replacements you had
    $string = str_replace("\\032", " ", $string); // Substituting octal 032 with its equivalent ASCII using chr()
    $string = str_replace("\\064", "@", $string); // @ symbol for octal 064
    $string = str_replace("\\092", "\\", $string); // Backslash for octal 092 (redundant with above)

    $string = str_replace("\\010", chr(10), $string); 
    $string = str_replace("\\009", chr(9), $string); 

    // Hexadecimal replacements
    $pattern = '/\\\\([0-9]{3})/';
    $string = preg_replace_callback($pattern, function ($matches) {
        return mb_chr(intval($matches[1]), "UTF-8");
    }, $string);
  
    return $string;
  }
 
}