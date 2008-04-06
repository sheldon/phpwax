<?php
/**
 * 
 *
 * @author Ross Riley
 * @package PHP-Wax
 **/

/**
 * Route construction class
 *
 * @package PHP-Wax
 * @author Ross Riley
 * 
 * This class allows urls to be mapped specifically to controllers actions and variables
 * It also requires access to the config object to check configurations.
 **/
class WaxUrl {
  
  
  /**
   *  This is simply a stackable array of mappings - new mappings are added to the top of the stack
   *  The lookup keeps going till it gets a match, falling back on the two defaults where necessary.
   *
   * @var array
   **/
  static $mappings = array(
    array("", array("controller"=>"page")),
    array(":controller/:action/:id")
  );



  
  /**
   *  Can be called from anywhere in the application. Maps a url to a particular outcome.
   * 
   *  Some examples.....
   *  WaxUrl::map(":controller/:action/:id") 
   *    - A default if no outcome is specified the variables after the colon are named with the values
   *
   *  WaxUrl::map(":controller/:action/:id", array("controller"=>"blog"))
   *    - A catch-all controller anything that can't find a controller will be mapped to the blog controller
   *
   *  WaxUrl:map("", array("controller"=>"page"))
   *    - Maps an empty url to a default controller - default action will be index but this can also be overwritten
   *
   *  WaxUrl::map("/tags/:tags*", array("controller"=>"tags", "action"=>"show"))
   *    - Looks for trigger pattern and then returns an array of the named parameter
   *
   *  WaxUrl::map("files/:file", array("controller"=>"file"), array("file"=>"^\w*\.doc|zip|jpg|gif"))
   *    - Using the conditions array allows you to provide a pattern that a parameter must match
   *
   * @return void
   **/
  
  static public function map($pattern, $outcome=array(), $conditions=array()) {
    array_unshift(self::$mappings, array($pattern, $outcome, $conditions));
  }
  
  
  /**
   *  Loops through the defined lookup patterns until one matches
   *  Uses the result to set the global $_GET parameters
   *  Any url variables that are explicitly set are ignored, this only works on the url portion
   *
   * @return void
   **/

  static public function perform_mappings($pattern) {
    foreach(self::$mappings as $map) {
      $left = $map[0];
      $right = $_GET["route"];
      $left = preg_replace("/:([A-Za-z0-9\-]*\*)/", "([A-Za-z0-9\-\/]*)", $left);
      $left = preg_replace("/:([A-Za-z0-9\-]*)/", "([A-Za-z0-9\-]*)", $left);
      $left = str_replace("/", "\/", $left);  
      echo $left."  :  ".$right."\n";    
      if($left===$right && !strpos($left,":")) $mapped_route = $map[1];
      elseif(preg_match("/".$left."/", $right, $matches)) {
        $mappings = split("/", $map[0]);
        array_shift($matches);
        while(count($mappings)) {
          if(substr($mappings[0],0,1)==":" && substr($mappings[0],-1)=="*") {
            $mapped_route[substr($mappings[0],1, -1)]=explode("/", $matches[0]);
          }
          elseif(substr($mappings[0],0,1)==":") {
            $mapped_route[substr($mappings[0],1)]=$matches[0];
            array_shift($matches); 
          }
          array_shift($mappings);
        }
        $mapped_route = array_merge($mapped_route, (array) $map[1]);
      }
      // Map against named parameters in options array
      
      if($mapped_route) {
        foreach($mapped_route as $k=>$val) {
          $_GET[$k]=$val;
        }
      break;
      }
    }
  }
  
  
  
  
  /**
   * get function
   *
   * @return mixed
   **/
  static public function get($val) {
    return $_GET[$val];
  }
  	
}

?>