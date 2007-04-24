<?php
require_once "WXValidations.php";

/*
 * @package PHP-Wax
 *
 * This class allows you to validate without persisting to the database.
 * You can optionally persist to session storage. 
 */
class WXForm extends WXValidations {
  
  protected $row = array();
  protected $persist = false;
  protected $form_name = "";
  
  public function __construct($persist=false) {
    if($persist) $this->persist = true;
    $this->form_name = "wx_form_".WXInflections::underscore(get_class($this));
    if($vals = Session::get($this->form_name)) $this->row = $vals;
  }
  
  public function save() {
    $this->validations();
		if(!$this->validate()) return false;
		if($this->persist) Session::set($this->form_name, $this->row);
  }
  
  public function validations() {}
  
  public function __get( $name ) {
    if( array_key_exists( $name, $this->row ) ) {
    	return $this->row[$name];
    }
  }
  
  public function __set($name, $value) {
    $this->row[$name] = $value;
  }
  
  /* Overridden as unique doesn't apply to non database validation*/
  public function valid_unique() {}
  
}