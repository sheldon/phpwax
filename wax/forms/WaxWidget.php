<?php


/**
 * Base Widget class
 *
 * @package PHP-Wax
 **/
class WaxWidget {

  public $allowable_attributes = array(
    "type", "name", "value", "checked", "disabled", "readonly", "size", "id", "class",
    "maxlength", "src", "alt", "accesskey", "tabindex", "rows", "cols", "multiple"
  );
  
  public $defaults = array("name"=>"","editable"=>true,"value"=>"");
  

  public $label_template = '<label for="%s>%s</label>';
  public $template = '<input %s />%s';
  public $error_template = '<span class="error_message">%s</span>';
  public $bound_data = false;
  
  
  public function __construct($name, $data=false) {
    if($data instanceof WaxModelField) $this->bound_data = $data;
    elseif(is_array($data)) {
      $this->defaults["name"]=$name;
      $this->defaults["id"]=$name."_id";
      $this->defaults["label"]=Inflections::humanize($name);
      $settings = array_merge($this->defaults, $data);
      foreach($settings as $datum=>$value) $this->$datum = $value;
    }
    else {
      $this->name = $name;
      $this->id = $name."_id";
      $this->editable = true;
      $this->label = Inflections::humanize($name);
    }
  }
  
  
  public function render() {
    if(!$this->editable) return false;
    $out ="";
    $out .= $this->before_tag();
    if($this->errors) $this->class.=" error_field";
    if($this->label) $out .= sprintf($this->label_template, $this->id, $this->label); 
    $out .= sprintf($this->template, $this->make_attributes(), $this->tag_content());
    if($this->bound_data && $this->errors) {
      foreach($this->errors as $error) $out .= sprintf($this->error_template, $error);
    }
    $out .= $this->after_tag();
    return $out;
  }
  
  public function attribute($name, $value) {
    $this->$name = $value;
  }
  
  public function make_attributes() {
    $res = "";
    foreach($this->allowable_attributes as $name) {
      if($this->{$name}) $res.=sprintf('%s="%s" ', $name, $this->{$name});
    }
    return $res;
  }
  
  public function before_tag(){}
  public function after_tag(){}
  public function handle_post($post_val){
    $this->value = $post_val;
    return $this->value;
  }
  public function get_choices(){ return array();}
  
  public function tag_content() {
    return true;
  }
  
  public function is_valid() {
    if(count($this->errors)>0) return false;
    return true;
  }
  
  public function __get($value) {
    if(!$this->bound_data) return false;
    if($this->bound_data instanceof WaxModelField) {
      return $this->bound_data->{$value};
    }
    if(is_array($this->bound_data)) return $this->bound_data[$value];
  }
  
  public function __set($name, $value) {
    if($this->bound_data instanceof WaxModelField) {
      $this->bound_data->{$name}=$value;
    } else $this->{$name}=$value;
  }



} // END class 