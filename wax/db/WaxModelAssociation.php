<?php

/**
 *  WaxModelAssociation Extends Recordset class
 *  Adds specific methods to associated model sets
 *
 * @package PhpWax
 **/

class WaxModelAssociation extends WaxRecordset {
  
  public $join_model;
  public $owner_field;

  public function __construct(WaxModel $model, WaxModel $join_model, $rowset, $owner_field=false) {
    $this->rowset = $rowset;
    $this->model = $model;
    $this->join_model = $join_model;
    $this->owner_field = $owner_field;
  }

  public function __call($method, $args) {
    return call_user_func_array(array($this->join_model->get_col($this->owner_field), $method), $args);
  }
  
  public function offsetGet($offset) {
    $join_model = get_class($this->join_model);
    print_r($this); exit;
    if(is_numeric($this->rowset[$offset])) return new $join_model($this->rowset[$offset]);
    $obj = clone $this->model;
    $obj->set_attributes($this->rowset[$offset]);
    return $obj;
  }

  
}