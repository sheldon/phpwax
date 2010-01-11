<?php

/**
 * ForeignKey class
 *
 * @package PHP-Wax
 **/
class ForeignKey extends WaxModelField {
  
  public $maxlength = "11";
  public $target_model = false;
  public $widget = "SelectInput";
  public $choices = array();
  public $is_association = true;
  public $data_type = "integer";
  
  public function setup() {
    if(!$this->target_model) $this->target_model = Inflections::camelize($this->field, true);
    // Overrides naming of field to model_id if col_name is not explicitly set
    if($this->col_name == $this->field){
      $link = new $this->target_model;
      $this->col_name = Inflections::underscore($this->target_model)."_".$link->primary_key;
    }
  }

  public function validate() {
    return true;
  }
  
  public function get() {
    if($this->model->row[$this->col_name] instanceof WaxModel) return parent::get();
    else{
      $model = new $this->target_model(parent::get());
      if($model->pk()) return $model;
    }
    return false;
  }
  
  public function get_choices() {
    if($this->choices && $this->choices instanceof WaxRecordset) {
      foreach($this->choices as $row) $choices[$row->{$row->primary_key}]=$row->{$row->identifier};
      $this->choices = $choices;
      return true;
    }
    $link = new $this->target_model;
    $this->choices[""]="Select";
    foreach($link->all() as $row) $this->choices[$row->{$row->primary_key}]=$row->{$row->identifier};
    return $this->choices;
  }
  
} 
