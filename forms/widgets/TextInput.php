<?php


/**
 * Text Input Widget class
 *
 * @package PHP-Wax
 **/
class TextInput extends WaxWidget {

  public $type="text";
  public $class = "input_field text_field";
  
  public $label_template = '<span><label for="%s">%s</label>';
  public $template = '<input %s /></span>';
  
} // END class