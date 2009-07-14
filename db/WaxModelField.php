<?php

/**
 * WaxModelFields class
 *
 * @package PHP-Wax
 **/
class WaxModelField {
    
  // Database Specific Configuration
  public $field = false;          // How this column is referred to
  public $null = true;           // Can column be null
  public $default = false;       //default value for the column  
  public $primary_key = false;  // the primay key field name - der'h
  public $table = false;          // Table name in the storage engine
  public $col_name;               // Actual name in the storage engine
  
  //Validation & Format Options
  public $maxlength = false; 
  public $minlength = false;
  public $choices = false; //for select fields this is an array
  public $text_choices = false; // Store choices as text in database
  public $editable = true; // Only editable options will be displayed in forms
  public $blank = true; 
  public $required = false; 
  public $show_label = true;
  public $label = false;
  public $help_text = false;
  public $widget="TextInput";
  public $is_association=false; // Distiguishes between standard field and one that links to other models
  protected $model = false;
  public $validator = "WaxValidate";
  public $validations = array();
  
  public $errors = array();

  public function __construct($column, $model, $options = array()) {
    $this->model = $model;
    foreach($options as $option=>$val) $this->{$option} = $val;
    if(!$this->field) $this->field = $column;
    if(!$this->table) $this->table = $this->model->table;
    if(!$this->col_name) $this->col_name = $this->field;
    $this->setup();
    $this->map_choices();
    $this->setup_validations();
  }
  
  public function get() {
    return $this->model->row[$this->col_name];
  }
  
  public function value() {return $this->get();}
  
  public function set($value) {
    $this->model->row[$this->col_name]=$value;
  }
  
  public function before_sync() {}  
  public function setup() {}
  public function validate() {}
  public function save() {}
  public function delete(){}  
  public function output() {
    return $this->get();
  }
  
  public function map_choices() {
    if($this->text_choices && is_array($this->choices)) {
      $choices = $this->choices;
      $this->choices = array();
      foreach($choices as $key=>$choice) {
        if(is_numeric($key)) $this->choices[$choice]=$choice;
        else $this->choices[$key]=$choice;
      }
    }
  }
  
  public function setup_validations() {
    if($this->required) $this->validations[]="required";
    if($this->minlength) $this->validations[]="length";
    if($this->maxlength) $this->validations[]="length";
  }
  
  
  public function is_valid() {
    $this->validate();
    $validator = new $this->validator($this, $this->field);
    foreach($this->validations as $valid) $validator->add_validation($valid);
    $validator->validate();
    if($validator->is_valid() && (!$this->errors)) return true;
    else $this->errors = array_merge($this->errors,$validator->errors);
    return false;
  }
  
  
  protected function add_error($field, $message) {
    if(!in_array($message, (array)$this->errors)) $this->errors[]=$message;
 	}
 	
 	
 	public function __set($name, $value) {
    if($name=="value") $this->set($value);
 	}
 	
 	public function __get($value) {
 	  if($value =="value") return $this->output();
 	  if($value =="name") return $this->table."[".$this->field."]";
    if($value =="id") return $this->table."_{$this->field}";
 	}
  

} // END class 
