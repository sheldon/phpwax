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
  public $identifier = false;
  
  public function setup() {
    if(!$this->target_model) $this->target_model = Inflections::camelize($this->field, true);
    $link = new $this->target_model;
    if($this->model->identifier) {
      $this->choices[""]="Select";
      foreach($link->all() as $row) $this->choices[$row->{$row->primary_key}]=$row->{$this->model->identifier};
    }
    // Overrides naming of field to model_id if col_name is not explicitly set
    if($this->col_name == $this->field) $this->col_name = Inflections::underscore($this->target_model)."_".$link->primary_key;
  }

  public function validate() {
    return true;
  }
  
  public function get() {
    $class = $this->target_model;
    $this_class = get_class($this->model);
    $cache = WaxModel::get_cache($this_class, $this->field, $this->model->{$this->col_name});
    if($cache) return $cache;
    error_log("Failed lookup for model $this_class running query for ".$this->model->primval());
    $model = new $class($this->model->{$this->col_name});
    if($model->primval) {
      WaxModel::set_cache($this_class, $this->field, $this->model->{$this->col_name}, $model);
      return $model;
    } else return false;
  }
  
  public function set($value) {
    if($value instanceof WaxModel) {
      $this->model->{$this->col_name} = $value->{$value->primary_key};
      unset($this->model->{$this->field});
      return $this->model->save();
    }
  }
  
  public function save() {
    return true;
  }
  
  public function __get($name) {
    if($name == "value") return $this->model->{$this->col_name};
    return parent::__get($name);
  }


} 
