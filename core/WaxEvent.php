<?php

/**
 *### Application Event Class
 * 
 * @package PHP-Wax  
 * @author Ross Riley
 * 
 * This class allows events to be defined and callbacks attached to each event
 **/
class WaxEvent {
  
  // Event callbacks
  public static $events = array();

  // Cache of events that have been run
  private static $has_run = array();

  // Data that can be processed during events
  public static $data_array = array();


  // This is here purely for backwards compatability
  public static $data;

  /**
   * Add a callback to an event queue.
   *
   *  @param   `string`   event name  
   *  @param   `array`    [PHP Callback](http://php.net/callback)  
   *  @return  `boolean`
   */
   
  public static function add($name, $callback) {
    if( ! isset(self::$events[$name])) {
      // Create an empty event if it is not yet defined
      self::$events[$name] = array();
    } elseif (in_array($callback, self::$events[$name], TRUE)) {
      // The event already exists
      return FALSE;
    }

    // Add the event
    self::$events[$name][] = $callback;
    return TRUE;
  }

  /**
   * Add a callback to an event queue, before a given event.
   *
   * @param   string   event name  
   * @param   array    existing event callback  
   * @param   array    event callback  
   * @return  boolean
   */
  public static function add_before($name, $existing, $callback) {
    if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE) {
    	// Just add the event if there are no events
    	return self::add($name, $callback);
    } else {
    	// Insert the event immediately before the existing event
    	return self::insert_event($name, $key, $callback);
    }
  }
  
  /**
   * Add a callback to an event queue, after a given event.
   *
   * @param   string   event name  
   * @param   array    existing event callback  
   * @param   array    event callback  
   * @return  boolean
   */
  public static function add_after($name, $existing, $callback) {
    if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE) {
    	// Just add the event if there are no events
    	return self::add($name, $callback);
    } else {
    	// Insert the event immediately after the existing event
    	return self::insert_event($name, $key + 1, $callback);
    }
  }
  
  /**
   * Inserts a new event at a specfic key location.
   *
   * @param   string   event name  
   * @param   integer  key to insert new event at  
   * @param   array    event callback  
   * @return  void
   */
  private static function insert_event($name, $key, $callback) {
    if(in_array($callback, self::$events[$name], true)) return false;
    
    // Add the new event at the given key location
    array_splice(self::$events[$name], $key, 0, $callback);
    return true;
  }
  
  /**
   * Replaces an event with another event.
   *
   * @param   string   event name  
   * @param   array    event to replace  
   * @param   array    new callback  
   * @return  boolean
   */
  public static function replace($name, $existing, $callback) {
    if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name], TRUE)) === FALSE)
    	return FALSE;
    
    if ( ! in_array($callback, self::$events[$name], TRUE)) {
    	// Replace the existing event with the new event
    	self::$events[$name][$key] = $callback;
    } else {
    	// Remove the existing event from the queue
    	unset(self::$events[$name][$key]);
    
    	// Reset the array so the keys are ordered properly
    	self::$events[$name] = array_values(self::$events[$name]);
    }
    return TRUE;
  }
  
  /**
   * Get all callbacks for an event.
   *
   * @param   string  event name  
   * @return  array
   */
  public static function get($name) {
  	return empty(self::$events[$name]) ? array() : self::$events[$name];
  }
  
  /**
   * Clear some or all callbacks from an event.
   *
   * @param   string  event name  
   * @param   array   specific callback to remove, FALSE for all callbacks  
   * @return  void
   */
  public static function clear($name, $callback = FALSE) {
  	if ($callback === FALSE) {
  		self::$events[$name] = array();
  	} elseif (isset(self::$events[$name])) {
  		// Loop through each of the event callbacks and compare it to the
  		// callback requested for removal. The callback is removed if it matches.
  		foreach (self::$events[$name] as $i => $event_callback) {
  			if ($callback === $event_callback) unset(self::$events[$name][$i]);
  		}
  	}
  }
  
  /**
   * Execute all of the callbacks attached to an event.
   *
   * @param   string   event name  
   * @param   array    data can be processed as Event::$data_array by the callbacks  
   * @return  void
   */
  public static function run($name, & $data = NULL) {
  	if ( ! empty(self::$events[$name])) {
  		// So callbacks can access Event::$data_array
  		self::$data_array[] =& $data;
  		self::$data = & $data;
  		$callbacks  =  self::get($name);
  		foreach ($callbacks as $callback) call_user_func($callback);
  
  		// Do this to prevent data from getting 'stuck'
  		array_pop(self::$data_array);
  	}
  
  	// The event has been run!
  	self::$has_run[$name] = $name;
  }
  
  /**
   * Get data from top of $data array stack.
   *
   * @return  mixed
   */
  public function &data() {
    end(self::$data_array);
    return self::$data_array[key(self::$data_array)];
  }
  
  /**
   * Check if a given event has been run.
   *
   * @param   string   event name  
   * @return  boolean
   */
  public static function has_run($name) {
  	return isset(self::$has_run[$name]);
  }
  
  	
}

