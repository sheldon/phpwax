<?php

/**
 * Cache Helper....
 * Allows capturing of output to cache
 *
 * @package default
 * @author Ross Riley
 */

class WaxCacheHelper extends WXHelpers {
  
  public $cache = false;

  
  protected function make_id($label) {
    return "helper_".str_replace("/","_",$label);
  }

  public function cache_start($label) {
    global $cache;
    global $cache_reading;
    $cache = new WaxCacheFile();
    $cache->identifier = $cache->dir.$this->make_id($label);
    ob_start();    
    if($cache->get()) {
      $cache_reading = true;
      return false;
    }
    return true;
  }
  
  
  public function cache_end() {
    global $cache;
    global $cache_reading;
    if($cache_reading) {
      $cache_reading = false;
      ob_end_clean();
      echo $cache->get();
      return true;
    }
    $content = ob_get_contents();
    $cache->set($content);
    ob_end_flush();
  }
  
  public function cache_valid($label) {
    $cache = new WaxCacheFile();
    $cache->identifier = $cache->dir.$this->make_id($label);
    return $cache->get();  
  }

  public function cache_get($label) {
    $cache = new WaxCacheFile();
    $cache->identifier = $cache->dir.$this->make_id($label);
    return $cache->get();  
  }
  


}