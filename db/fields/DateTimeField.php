<?php

/**
 * EmailField class
 *
 * @package PHP-Wax
 **/
class DateTimeField extends WaxModelField {
  
  public $null = true;
  public $default = false;
  public $maxlength = false;
  public $widget = "DateInput";
  public $output_format = "Y-m-d H:i:s";
  public $save_format = "Y-m-d H:i:s";
  public $use_uk_date = false;

  
  public function setup() {
    if($this->model->row[$this->field]==0 && $this->default=="now") {
      $this->model->row[$this->field] = date($this->save_format);
    }
    if($this->required) $this->validations["datetime"];
  }
  
  public function output() {
    return date($this->output_format, strtotime($this->get()));
  }
  
  public function save() {
    if($this->required) $this->model->row[$this->field]= date($this->save_format, strtotime($this->get()));    
  }
  
  public function uk_date_switch() {
    
  }
  

} 