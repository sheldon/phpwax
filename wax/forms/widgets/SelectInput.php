<?php


/**
 * Text Input Widget class
 *
 * @package PHP-Wax
 **/
class SelectInput extends WaxWidget {

  public $class = "input_field select_field";
  public $label_template = '<label for="%s">%s</label>';
  public $template = '<select %s>%s</select>';
  public $type=false;
  
  
  public function tag_content() {
    return $this->make_choices();
  }
  
  public function make_choices() {
    $output = "";
    $choice = '<option value="%s"%s>%s</option>';
    foreach($this->choices as $value=>$option) {
      $sel = "";
      if($this->value==$value) $sel = ' selected="selected"';
      $output .= sprintf($choice, $value, $sel, $option);
    }
    return $output;
  }
  



} // END class