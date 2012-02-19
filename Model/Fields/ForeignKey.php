<?php
namespace Wax\Model\Fields;
use Wax\Model\Field;
use Wax\Model\Model;
use Wax\Model\Recordset;
use Wax\Template\Helper\Inflections;

/**
 * ForeignKey class
 *
 * @package PHP-Wax
 **/
class ForeignKey extends Field {
  
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
    $class = $this->target_model;
    $model = new $this->target_model($this->model->{$this->col_name});
    if($model->primval) {
      return $model;
    } else return false;
  }
  
  public function set($value) {
    if($value instanceof Model) {
      $this->model->{$this->col_name} = $value->{$value->primary_key};
      return $this->model->save();
    } else {
      $this->model->{$this->col_name} = $value;
      return $this->model->save();
    }
  }
  
  public function save() {
    return true;
    //return $this->set($this->value);
  }
  
  public function get_choices() {
    if($this->choices && $this->choices instanceof Recordset) {
      foreach($this->choices as $row) $choices[$row->{$row->primary_key}]=$row->{$row->identifier};
      $this->choices = $choices;
      return true;
    }
    $link = new $this->target_model;
    $this->choices[""]="Select";
    foreach($link->all() as $row) $this->choices[$row->{$row->primary_key}]=$row->{$row->identifier};
    return $this->choices;
  }
  
  public function __get($name) {
    if($name == "value") return $this->model->{$this->col_name};
    return parent::__get($name);
  }


} 
