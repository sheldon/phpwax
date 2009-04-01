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
  public $label = true; // Set to false to never show labels
  public $help_text = false;
  public $widget="TextInput";
  public $is_association=false; // Distiguishes between standard field and one that links to other models
  protected $model = false;
  
  public $errors = array();
  //errors messages
  public $messages = array(
    "short"=>       "%s needs to be at least %d characters",
    "long"=>        "%s needs to be shorter than %d characters",
    "required"=>    "%s is a required field",
    "unique"=>      "%s has already been taken",
    "confirm"=>     "%s and %s do not match",
    "format"=>      "%s is not a valid %s format"
  );
  
  

  public function __construct($column, $model, $options = array()) {
    $this->model = $model;
    foreach($options as $option=>$val) $this->{$option} = $val;
    if(!$this->field) $this->field = $column;
    if(!$this->table) $this->table = $this->model->table;
    if(!$this->col_name) $this->col_name = $this->field;
    if($this->label===true) $this->label = Inflections::humanize($this->field);
    $this->errors = $this->model->errors[$this->field];
    $this->setup();
    $this->map_choices();
  }
  
  public function get() {
    return $this->model->row[$this->col_name];
  }
  
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
 	
 	
 	/**
 	 *  Default Validation Methods
 	 */
 	 
  protected function valid_length() {
    if($this->minlength && strlen($this->model->{$this->field}) < $this->minlength) {
      $this->add_error($this->column, sprintf($this->messages["short"], $this->label, $this->minlength));
    }
    if($this->maxlength && strlen($this->model->{$this->field})> $this->maxlength) {
      $this->add_error($this->column, sprintf($this->messages["long"], $this->label, $this->maxlength));
    }
  }
  protected function valid_float(){
		$lengths = explode(",", $this->maxlength);
		$values = explode(".", $this->model->{$this->field});
		if(strlen($values[0]) > $lengths[0]){
			$this->add_error($this->column, sprintf($this->messages["long"], $this->label, $this->minlength));
		}
		if($values[1] && $lengths[1] && strlen($values[1]) > $lengths[1]){
			$this->add_error($this->column, sprintf($this->messages["long"], $this->label, $this->minlength));
		}
	}

  protected function valid_format($name, $pattern) {
    if(!preg_match($pattern, $this->model->{$this->field})) {
      $this->add_error($this->column, sprintf($this->messages["format"], $this->label, $name));
		}
  }
  
  protected function valid_required() {
    if(!$this->blank && strlen($this->model->{$this->field})< 1) {
      $this->add_error($this->field, sprintf($this->messages["required"], $this->label));
    }
  }
  
  protected function valid_confirm($confirm_field, $confirm_name) {
    if($this->model->{$this->field} != $this->model->{$confirm_field}) {
      $this->add_error($this->field, sprintf($this->messages["confirm"], $this->label, $confirm_name));
    }
  }
  

} // END class 
