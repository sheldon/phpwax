<?php
/**
 * 
 *
 * @author Ross Riley
 * @package php-wax
 **/

/**
 * Route construction class
 *
 * @package php-wax
 * @author Ross Riley
 * 
 * This class fetches the URL parameters from $_GET
 * It also requires access to the config object to check configurations.
 **/
class WXRoute extends ApplicationBase
{
	protected $route_array=array();
	protected $config_array=array();
	protected $actions_array=array();
	
	public function __construct() {
		$this->route_array=array_values(array_filter(explode("/", $_GET['route'])));
		$this->config_array=WXConfiguration::get('route');
		$this->map_routes();		
	}
	
	
	/**
    *  In the configuration file you can setup a section called 'route'
    *  this allows you to magically rewrite the request to anything you like. 
    *  
    *  The left hand side specifies a match, the right hand side is the new output.
    *  for example, - admin/login: page/login - will rewrite the url from the left to the right.
    *  Hell, if you fancy it you can even include the '*' wildcard. -admin/* : page/*
    *  
    *  An additional default route can be provided to catch any missing controllers.
    *
    *  @return void
    */
    
	public function map_routes() {
	  
	}
	
	public function pick_controller() {
	  if( array_key_exists($this->route_array[0], $this->config_array) ) {
	    $this->route_array[0]=$this->config_array[$this->route_array[0]];
	  }
	  if(empty($this->route_array)) $this->route_array[0]=$this->config_array['default'];
	  if(is_dir(CONTROLLER_DIR.$this->route_array[0])) {
    	$this->route_array[1]=$this->route_array[0]."/".$this->route_array[1]."/";
    	array_shift($this->route_array);
    }
	  if($res = $this->check_controller($this->route_array[0])) return $res;
	}
	
	/**
    *  Checks whether a file exists for the named controller
    *  @return boolean      If file exists true
    */
	private function check_controller($controller) {
		if(strpos($controller, "/")) {
			$path = substr($controller, 0, strpos($controller, "/")+1);
			$class = slashcamelize($controller, true)."Controller";
			if(is_file(CONTROLLER_DIR.$path.$class.".php")) return $class;
		}
		$class = ucfirst($controller)."Controller";
		$default = ucfirst($this->config_array['default']."Controller");
		if(is_file(CONTROLLER_DIR.$class.".php")) return $class;
		if(is_file(CONTROLLER_DIR.$default.".php")) {
		  array_unshift($this->route_array, $this->config_array['default']);
		  return $default;
	  }
		throw new WXException("Missing Controller - ".$class, "Controller Not Found");
	}
	
	/**
    *  Strips the controller from the route and returns an array of actions
    *  This is designed to be called from the delegate controller.
    *
    *  @return boolean      If file exists true
    */
	
	public function read_actions() {
		return array_shift($this->route_array);
	}
	
	public function get_url_controller() {
		$url = str_replace("Controller", "", $this->pick_controller());
		return slashify($url);
	}
	
	
	
} // END class 

?>