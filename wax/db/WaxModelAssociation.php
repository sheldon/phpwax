<?php

/**
 *  WaxModelAssociation Extends Recordset class
 *  Adds specific methds to associated model sets
 *
 * @package PhpWax
 **/

class WaxModelAssociation extends WaxRecordset {

  public function __construct($model, $field) {
    parent::__construct($model, $model->all()->rowset);
    $this->field = $field;
    print_r($this);
  } 
  
  public function unlink($model) {
    $this->model->get_col(field)->unlink($model);
    return $this->model;
  }
  

  
  
}