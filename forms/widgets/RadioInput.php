<?php


/**
 * Radio Input Widget class
 *
 * @package PHP-Wax
 **/
class RadioInput extends SelectInput {

  public $type="radio";
  public $class = "input_field radio_field";
  
  public $template = '%s';
  public $group_label_class = "radio_group_label";
  public $label_template = '<label for="%s" class="%s">%s</label>';
  public $sub_template = '<span class="radio"><input type="radio" name="%s" value="%s" %s id="%s"><label for="%s" class="radio_label">%s</label></span>';

  public function tag_content() {
    $output = "";
    if(!$this->choices) $this->choices = $this->get_choices();
    $this->map_choices();
    foreach($this->choices as $value=>$option) {
      $sel = "";
			if(is_numeric($this->value) && (int)$this->value==(int)$value) $sel = ' checked="checked"';
			elseif( (string)$this->value==(string) $value) $sel = ' checked="checked"';
      $output .= sprintf($this->sub_template, $this->output_name(), $value,$sel,$this->output_id().$value,$this->output_id().$value,$option);
    }
    return $output;
  }
  
  public function render($settings = array(), $force=false) {
    foreach($settings as $set=>$val) $this->{$set}=$val;
    if(!$this->editable && !$force) return false;
    $out ="";
    $out .= $this->before_tag();
    if($this->errors) $this->add_class("error_field");
    if($this->show_label) $out .= $this->label();
    $out .= sprintf($this->template, $this->tag_content());
    if($this->errors && $this->inline_errors){
      foreach($this->errors as $error) $out .= sprintf($this->error_template, $error);
    }
    $out .= $this->after_tag();
    return $out;
  }
  
  public function map_choices() {
    foreach($this->choices as $key=>$choice) {
      if($this->text_choices) $choices[$choice]=$choice;
      else $choices[$key]=$choice;
    }
    $this->choices = $choices;
  }
  
  public function label() {
    return sprintf($this->label_template, $this->output_id(), $this->group_label_class, $this->label);
  }
  

}
