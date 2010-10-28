<?php
/**
 * Model with improved tree handling speed by implementing using a closure table
 *
 * @package PHP-Wax
 * @author Sheldon Els
 *
 **/
class WaxClosureTree extends WaxModel {
  public $closure_table_class = "WaxClosureTable";
  
  /**
   * returns an empty closure table model
   */
  public function closure_table(){
    $closure_table = new $this->closure_table_class;
    $closure_table->init($this);
    return $closure_table;
  }
  
  /**
   * returns a WaxRecordSet based on the path from this node back to it's root
   * accepts a depth parameter which sets how far back in the ancestry you want to return, if left null it will return all the way back to the root
   */
  public function ancestors($depth = null){
    $res = clone $this;
		$res->clear();
		$res->left_join($this->closure_table());
		$res->join_condition("$this->table.$this->primary_key = ancestor_id");
		$res->order("depth");
    $res->filter("descendant_id",$this->primval());
		$res->select_columns = array($res->table.".*");
    if($depth !== null) $res->limit($depth);
    return $res->all();
  }
  
  /**
   * returns the parent of the current node
   */
  public function parent(){
    $ret = $this->ancestors(2);
    return $ret[$ret->count() - 1];
  }

  /**
   * returns a WaxRecordSet of all the descendants of the current node, it is effectively the entire subtree of the given node
   * accepts a depth parameter which sets how deep in the subtree you want to return, if left null it will return all the way to bottom
   */
  public function descendants($depth = null){
    $res = clone $this;
		$res->clear();
		$res->left_join($this->closure_table());
		$res->join_condition("$this->table.$this->primary_key = descendant_id");
		$res->order("depth");
		$res->filter("ancestor_id",$this->primval());
		$res->select_columns = array($res->table.".*");
    if($depth !== null) $res->filter("depth",$depth,"<=");
    return $res->all();
  }

  /**
   * returns the direct children of the current node
   */
  public function children(){
    $ret = $this->descendants($this->level+1);
    array_shift($ret->rowset);
    return $ret;
  }
  
  /**
   * used to implement setting parent or children
   * makes sure that anything in the ancestors doesn't exist in the subtree, causing recursion
   */
	public function __set($name, $value){
	  if($name == "parent") return $this->set_parent($value);
	  elseif($name == "children") return $this->set_children($value);
	  else return parent::__set($name, $value);
  }
  
  /**
   * add children to the current node
   * returns the value it's setting (similar to how other setters in php work, setters return the value they set)
   */
  private function set_children($child){
    if($child instanceof Iterator) foreach($child as $child_node) $this->set_children($child_node);
    if($child instanceof WaxClosureTree){
      $this->reparent($this,$child);
      return $child;
    }
  }
  
  /**
   * set the parent of the current node, otherwise known as reparenting or moving a sub tree
   * returns the parent node
   */
  private function set_parent(WaxClosureTree $new_parent){
    if($new_parent instanceof WaxClosureTree){
      $this->reparent($new_parent,$this);
      return $child;
    }
  }
  
  /**
   * reattach a subtree to a new node
   */
  private function reparent(WaxClosureTree $new_parent){
    $ancestors = $this->ancestors();
    $ancestor_ids = array();
    foreach($ancestors as $asc) $ancestor_ids[] = $asc->id;
    
    $descendants = $this->descendants();
    $descendant_ids = array();
    foreach($descendants as $des) $descendant_ids[] = $des->id;

    //first isolate the subtree, removing links from all ancestors to all descendants
    if($ancestor_ids && $$ancestor_ids) $closure = $this->closure_table()->filter("ancestor_id", $ancestor_ids)->filter("descendant_id", $$ancestor_ids)->delete();
    
    //add each descendant to each ancestor
    //INSERT INTO CLOSURE_TABLE cross product of $new_parent->ancestors() and $this->descendants()
    //this way is super slow, need a way to do the inserts as 1 query
    foreach($new_parent->ancestors() as $ancestor){
      foreach($descendants as $descendant){
        $link = $this->closure_table();
        $link->ancestor = $ancestor;
        $link->descendant = $descendant;
        $link->depth = $ancestor->depth + $descendant->depth + 1;
        $link->save();
      }
    }
  }
  

  
  /**
   * get the root nodes
   */
  public function roots() {
    
  }

  /**
   * returns true if the node has a parent, and false if not
   */
  public function is_root() {
  }

  /**
   * returns the root of the current node
   */
  public function root() {
    $path_from_root = $this->ancestors()->reset();
  }
  
  /**
   * gets the other nodes that are children of this nodes parent
   */
  public function siblings() {
  }
  
  /**
   * if it doesn't exist, creates the closure table and closes all nodes over themselves
   */
  public function syncdb(){
    if(get_class($this) == "WaxClosureTree") return;
    parent::syncdb();
    $this->closure_table()->syncdb();
  }
  
  /**
   * close each entry over itself
   */
  public function after_insert(){
    $new_closure = $this->closure_table();
    $new_closure->ancestor = $this;
    $new_closure->descendant = $this;
    $new_closure->depth = 0;
    $new_closure->save();
  }
}
?>