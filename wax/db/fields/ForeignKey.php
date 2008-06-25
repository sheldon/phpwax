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
    if($cache) {
      $id = $this->model->{$this->col_name};
      error_log("********RETURNING $this_class / $id from cache");
      return $cache;
    }
    $id = $this->model->{$this->col_name};
    error_log("********Can't Find $this_class / $id in cache");
    $model = new $class($this->model->{$this->col_name});
    if($model->primval) {
      WaxModel::set_cache($this_class, $this->field, $this->model->{$this->col_name}, $model);
      return $model;
    } else return false;
  }
  
  public function set($value) {
    if($value instanceof WaxModel) {
      $this->model->{$this->col_name} = $value->{$value->primary_key};
      return $this->model->save();
    } else {
      $obj = new $this->target_model($value);
      if($obj->primval) {
        $this->model->{$this->col_name} = $value;
        return $this->model->save();
      }
    }
  }
  
  public function save() {
    return true;
  }
  
  public function get_choices() {
    if($this->model->identifier) {
      $this->choices[""]="Select";
      foreach($link->all() as $row) $this->choices[$row->{$row->primary_key}]=$row->{$this->model->identifier};
    }
    return $this->choices;
  }
  
  public function __get($name) {
    if($name == "value") return $this->model->{$this->col_name};
    return parent::__get($name);
  }


} 
