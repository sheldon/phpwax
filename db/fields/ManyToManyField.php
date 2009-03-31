<?php

/**
 * ManyToManyField class
 *
 * @package PHP-Wax
 **/
class ManyToManyField extends WaxModelField {
  
  public $maxlength = "11";
  public $target_model = false; //model on the other side of the many to many
  public $join_model = false; //instance of WaxModelJoin filtered with this instance's primary key
  public $join_model_class = "WaxModelJoin";
  public $widget = "MultipleSelectInput";
  public $use_join_select = true;
	public $eager_loading = false;
  public $is_association = true;
	public $join_table = false; //this chap means that you can pass any name for the join table in on define()
	
	
	/**
	 * the setup function for this field is different, as this is a many to many relationship it takes into 
	 * account both sides of the relationship, initialises the join table if its missing and preps the 
	 * filter
	 */
  public function setup() {
    $this->col_name = false;
    if(!$this->target_model) $this->target_model = Inflections::camelize($this->field, true);
    if($this->model->primval) $this->setup_join_model();
  }

  public function setup_join_model() {
    $j = new $this->target_model;
    if(strnatcmp($this->model->table, $j->table) <0) {
      $left = $this->model;
      $right = $j;
    } else {
      $left = $j;
      $right = $this->model;
    }
    $join = new $this->join_model_class();
    $join->table = $this->join_table;
    $join->init($left, $right);
    $this->join_model = $join->filter(array($this->join_field($this->model) => $this->model->primval));
  }

  public function validate() {
    return true;
  }
  
  public function before_sync() {
    $this->setup_join_model();
   	return $this->join_model->syncdb();
  }
    
  /**
	 * Reads the load strategy from the setup and delegates either to eager_load or lazy load
	 * @return WaxModelAssociation
	 */	
  public function get($filters = false) {
    $target = new $this->target_model;
    if($filters){
      $target->filter($filters);
      $this->eager_loading = true; //forcing eager loading when doing join crossing filters, this means lazy loading does no database joins at all and stays light, as it should be
    }
    if(!$this->model->primval) return new WaxModelAssociation($this->model, new $this->target_model, array(), $this->field);
    if($this->eager_loading) return $this->eager_load($target);
    return $this->lazy_load($target);
  }


  private function eager_load($target) {
		$this->join_model->left_join($target);
		$this->join_model->join_condition("$target->table.$target->primary_key = ".$this->join_model->table.".".$this->join_field($target));
		//select columns from the far side of the join, not the join table itself
		$this->join_model->select_columns = array($target->table.".*");
		$cache = WaxModel::get_cache(get_class($this->model), $this->field, $this->model->primval.":".md5(serialize($target->filters)),$vals->rowset, false);
		if($cache) return new WaxModelAssociation($this->model, $target, $cache, $this->field);
		
		$vals = $this->join_model->all();
		WaxModel::set_cache(get_class($this->model), $this->field, $this->model->primval.":".md5(serialize($target->filters)), $vals->rowset);
		return new WaxModelAssociation($this->model, $target, $vals->rowset, $this->field);
  }
  
  private function lazy_load($target_model) {
    $left_field = $this->model->table."_".$this->model->primary_key;
    $right_field = $target_model->table."_".$target_model->primary_key;
    $this->join_model->select_columns=$right_field;
    $ids = array();
    if($cache = WaxModel::get_cache(get_class($this->model), $this->field, $this->model->primval.":".md5(serialize($target_model->filters)), false )) return new WaxModelAssociation($this->model, $target_model, $cache, $this->field);
    foreach($this->join_model->rows() as $row) $ids[]=$row[$right_field];
    WaxModel::set_cache(get_class($this->model), $this->field, $this->model->primval.":".md5(serialize($target_model->filters)), $ids);
    return new WaxModelAssociation($this->model, $target_model, $ids, $this->field);
  }
  
  
	/**
	 * clever little function that sets values across the join so $origin->many_to_many = $value works like:
	 *  - loops each wax model (or element in recordset)
	 *  - creates a new record on the join table for each
	 *  - clears the cache (so that any 'gets' are accurate)
	 * @param mixed $value - waxmodel or waxrecordset
	 */	
  public function set($value) {
    if(!$this->model->primval){
      throw new WXException("ManyToMany set before Model Save", "Cannot set ".get_class($this->model)."->".$this->field." before saving ".get_class($this->model));
      return;
    }
    if($value instanceof WaxModel) {
      if(!($ret = $this->join_model->filter(array($this->join_field($value) => $value->primval) )->first())) {
        $new = array($this->join_field($value)=>$value->primval, $this->join_field($this->model) => $this->model->primval);
        $ret = $this->join_model->create($new);
      }
    }
    if($value instanceof WaxRecordset) {
      foreach($value as $join) {
        $existing = clone $this->join_model;
        $filter = false;
        // Check for an existing association
        $filter = array($this->join_field($join) => $join->primval);
        $existing = $existing->filter($filter)->all();
        if(!$existing->count()) {
          $new = array($this->join_field($join)=>$join->primval, $this->join_field($this->model) => $this->model->primval);
          $new_join = $this->join_model->create($new);
          $rowset[] = $new_join->row;
        }
      }
   	  $ret = new WaxRecordset(new $this->join_model_class, $rowset);
    }
    WaxModel::unset_cache(get_class($this->model), $this->field);
    return $ret;
  }
  /**
   * this unset function removes any link between the origin and target
   * again the cache is cleared so any 'get' calls return accurate data
   * @param string $model 
   */  
  public function unlink($model) {
    if(!$this->model->primval){
      throw new WXException("ManyToMany unlink before Model Save", "Cannot unlink from ".get_class($this->model)."->".$this->field." before saving ".get_class($this->model));
      return;
    }
    $links = new $this->target_model;

    WaxModel::unset_cache(get_class($this->model), $this->field);

    if($model instanceof WaxModel) {
      if(!$model->primval) throw new WXException("ManyToMany Delete not possible", "Cannot find ".get_class($this->model)."->".$this->field." joined object");
      $this->join_model->filter(array($links->table."_".$links->primary_key => $model->primval))->delete();
    }
    if($model instanceof WaxRecordset) {
      foreach($model as $obj) {
        if(!$obj->primval) throw new WXException("ManyToMany Delete not possible", "Cannot find ".get_class($this->model)."->".$this->field." joined object");
				else $filter[$links->table."_".$links->primary_key] = $obj->primval;
      }
      if(count($filter)) $this->join_model->filter("(".join(" OR ", $filter).")")->delete();
    }
    return $this->join_model;
  }
  
	/**
	 * as this is a many to many, the delete doesnt actually delete!
	 * instead it unlinks the join table & yes, the obligatory cache clearing 
	 */	
	public function delete(){
    WaxModel::unset_cache(get_class($this->model), $this->field);
		//delete join tables!
		$data = $this->get();
		if($data->count()) $this->unlink($data);
	}
	/**
	 * as a save on a many to many doesn't do anything, just return true
	 */
  public function save() {
    return true;
    //return $this->set($this->value);
  }
  
  /**
   * filter on the join_model
   * IMPORTANT: will only work with array filters, text filters are passed on to the target model as usual
   * ALSO IMPORTANT: will not work if any of the defined filters are on columns not in the join model, in that case it will also pass on to the target model as usual
   *
   * @return WaxModelAssociation
   */
  
  public function filter($params) {
    if(is_array($params)){
      foreach($params as $column => $value){
        if(!$this->join_model->columns[$column]) return $this->__call("filter", array($params));
      }
      $this->join_model->filter($params);
  		WaxModel::unset_cache(get_class($this->model), $this->field);
      return $this->get();
    }else return $this->__call("filter", array($params));
  }

	/**
	 * take the model and create a string version of the field to use in the join
	 * @param string $WaxModel 
	 */	
  protected function join_field(WaxModel $model) {
    return $model->table."_".$model->primary_key;
  }
	/**
	 * get the choices for the field
	 * @return array
	 */	
  public function get_choices() {
    if($this->model->identifier) {
      $this->choices[""]="Select";
      foreach($j->all() as $row) $this->choices[$row->{$row->primary_key}]=$row->{$row->identifier};
    }
    return $this->choices;
  }
  
	/**
	 * super smart __call method - passes off calls to the target model (deletes etc)
	 * @param string $method 
	 * @param string $args 
	 * @return mixed
	 */	
  public function __call($method, $args) {
    $assoc = $this->get();
    $model = clone $assoc->target_model;
    if($assoc->rowset)
      $model->filter(array($model->primary_key=>$assoc->rowset));
    else
      $model->filter("1 = 2");
    return call_user_func_array(array($model, $method), $args);
  }

} 
?>