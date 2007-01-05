<?php

class TestWXConfiguration extends WXTestCase 
{
    public function setUp() {
      WXConfiguration::set_environment('test');
    }
    
    public function tearDown() {
      
    }

  	public function test_replace_yaml() {
  	  
  	}

  	public function test_set() {
  	  WXConfiguration::set(array("test"=>"5"));
  	  $this->assertEqual(WXConfiguration::get('test'), 5);
  	}

    public function test_get($value) { 
  	  $config = WXConfiguration::get('all');
  	  $this->assertTrue(is_array($config));
  	}

  	public function test_set_environment($env) {
  	  
  	}

    
}
?>