<?php

/**
 * PrimaryKey class
 *
 * @package PHP-Wax
 **/
class ForeignKey extends WaxModelField {
  
  public $null = false;
  public $default = false;
  public $maxlength = "11";
  public $model_name = false;
  
  
  public function setup() {
    if(!$this->table) $this->table = $this->field;
    if(!$this->col_name) $this->col_name = $this->table."_id";
    if(!$this->model_name) $this->model_name = Inflections::camelize($this->table);
  }

  public function validate() {

  }
  
  public function get() {
    $model = new $this->model_name($this->model->{$this->col_name});
    return $model;
  }
  
  public function set() {
    
  }
  
  public function save() {
    if($this->model->{$this->field} instanceof WaxModel) {
      $this->model->{$this->col_name} = $this->model->{$this->field}->id;
      unset($this->model->{$this->field});
    }
  }


} 
