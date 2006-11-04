<?php
/*
 * @package wxframework.core
 *
 * This class is based in part on the helpers functionality in the PHP on Trax Framework. 
 * For more information, see:
 *  http://phpontrax.com/
 */

/**
 *  @todo Document this class
 */
class UrlHelper extends WXHelpers {
	

    /**
     * Creates a link tag of the given +name+ using an URL created by
     * the set of +options+. 
     * It's also possible to pass a string instead of an options hash
     * to get a link tag that just points without consideration. If
     * null is passed as a name, the link itself will become the
     * name. 
     * The $html_options have a special feature for creating
     * javascript confirm alerts where if you pass ":confirm" => 'Are
     * you sure?', 
     * the link will be guarded with a JS popup asking that
     * question. If the user accepts, the link is processed, otherwise
     * not. 
     *
     * Example:
     *   link_to("Delete this page", array(":action" => "delete",
     * ":id" => $page->id), array(":confirm" => "Are you sure?")) 
     *  @return string
     *  @uses content_tag()
     *  @uses convert_confirm_option_to_javascript()
     *  @uses url_for()
     */
    function link_to($name, $options = array(), $html_options = array()) {
        $html_options =
            $this->convert_confirm_option_to_javascript($html_options);
        if(is_string($options)) {
            $href = array("href" => $options);
            if(count($html_options) > 0) {
                $html_options = array_merge($html_options, $href);
            } else {
                $html_options = $href;
            }
            if(!$name) {
                $name = $options;
            }
            $html = $this->content_tag("a", $name, $html_options);
        } else {
            $url = $this->url_for($options);
            if(!$name) {
                $name = $url;
            }
            $href = array("href" => $url);
            if(count($html_options) > 0) {
                $html_options = array_merge($html_options, $href);
            } else {
                $html_options = $href;
            }
            $html = $this->content_tag("a", $name, $html_options);
        }
        return $html;
    }

    /**
     *  @todo Document this method
     *  @param string[] Options
     *  @return string
     */
    function convert_confirm_option_to_javascript($html_options) {
        if(array_key_exists('confirm', $html_options)) {
            $html_options['onclick'] =
                "return confirm('".addslashes($html_options['confirm'])."');";
            unset($html_options['confirm']);
        }
        return $html_options;
    }

    /**
     *  @todo Document this method
     *  @param mixed[]
     *  @param mixed[]
     *  @return mixed[]
     */
    function convert_boolean_attributes(&$html_options, $bool_attrs) {
        foreach($bool_attrs as $x) {
            if(@array_key_exists($x, $html_options)) {
                $html_options[$x] = $x;
            }
        }
        return $html_options;
    }

    /**
     *  @todo Document this method
     *  @param string
     *  @param mixed[]
     *  @param mixed[]
     *  @return string
     *  @uses convert_boolean_attributes()
     *  @uses convert_confirm_option_to_javascript()
     *  @uses url_for()
     */
    function button_to($name, $options = array(), $html_options = null) {
        $html_options = (!is_null($html_options) ? $html_options : array());
        $this->convert_boolean_attributes($html_options, array('disabled'));
        $html_options = $this->convert_confirm_option_to_javascript($html_options);
        if (is_string($options)) {
            $url = $options;
            $name = (!is_null($name) ? $name : $options);
        } else {
            $url = url_for($options);
            $name = (!is_null($name) ? $name : url_for($options));
        }

        $html_options = array_merge($html_options,
                              array("type" => "submit", "value" => $name));
        return "<form method=\"post\" action=\"" .  htmlspecialchars($url)
            . "\" class=\"button-to\"><div>"
            . $this->tag("input", $html_options) . "</div></form>";
    }

    

    /**
     *  Generate URL based on current URL and optional arguments
     *
     *  @param mixed[]
     *  <ul>
     *    <li><b>string:</b><br />
     *      The string value is returned immediately with no
     *      substitutions.</li>
     *    <li><b>array:</b>
     *     <ul>
     *       <li><samp>':controller'=></samp><i>controller value</i></li>
     *       <li><samp>':action'=></samp><i>action value</i></li>
     *       <li><samp>':id'=></samp><i>id value</i></li>
     *     </ul>
     *  </ul>
     *  @return string
     */
    function url_for($options = array()) {
        $url_base = null;
        $url = array();
        $extra_params = array();
        if(is_string($options)) {

            //  Argument is a string, just return it
            return $options;

        } elseif(is_array($options)) {

            //  Argument is a (possibly empty) array
            //  Start forming URL with this host
            $url_base = $_SERVER['HTTP_HOST'];
            if(substr($url_base, -1) == "/") {
                # remove the ending slash
                $url_base = substr($url_base, 0, -1);
            }           

            //  Method is same as was used by the current URL
            if($_SERVER['SERVER_PORT'] == 443) {
                $url_base = "https://".$url_base;
            } else {
                $url_base = "http://".$url_base;
            }

            
            //  Get controller from $options or $controller_path
            if(array_key_exists(":controller", $options)) {
                if($controller = $options[":controller"]) {
                    $url[] = $controller; 
                }
            } else {
                $controller = $_GET['route'];
                if(substr($controller, 0, 1) == "/") {
                    # remove the beginning slash
                    $controller = substr($controller, 1);        
                }
								if(substr($controller, -1) == "/") {
                    # remove the beginning slash
                    $controller = substr($controller, 0,-1);        
                }
                $url[] = $controller;
            }

            //  If controller found, get action from $options
            if(count($url)) {
                if(array_key_exists(":action", $options)) {
                    if($action = $options[":action"]) {
                        $url[] = $action;
                    }
                } 
            }

            //  If controller and action found, get id from $actions
            if(count($url) > 1) {
                if(array_key_exists(":id", $options)) {
                    if(is_object($options[":id"])) {
                        if($id = $options[":id"]->id) {
                            $url[] = $id;
                        }
                    } else {
                        if($id = $options[":id"]) {
                            $url[] = $id;
                        }
                    }
                }
            }
            
            if(count($options)) {
                foreach($options as $key => $value) {
                    if(!strstr($key, ":")) {
                        $extra_params[$key] = $value; 
                    }       
                }    
            }
        }
        
        if(count($url) && substr($url_base,-1) != "/") {
            $url_base .= "/";    
        } 
        return $url_base . implode("/", $url)
            . (count($extra_params)
               ? "?".http_build_query($extra_params) : null);
    }    

}

/**
 *  Make a new UrlHelper object and call its link_to() method
 *  @uses UrlHelper::link_to()
 */
function link_to($name, $options = array(), $html_options = array()) {
    $url_helper = new UrlHelper();
    return $url_helper->link_to($name, $options, $html_options);
}

/**
 *  Make a new UrlHelper object and call its link_image_to() method
 *  @uses UrlHelper::link_image_to()
 */
function link_image_to($src, $options = array(), $html_options = array()) {
    $url_helper = new UrlHelper();
    return $url_helper->link_image_to($src, $options, $html_options);        
}

/**
 *  Make a new UrlHelper object and call its button_to() method
 *  @uses UrlHelper::button_to()
 */
function button_to($name, $options = array(), $html_options = null) {
    $url_helper = new UrlHelper();
    return $url_helper->button_to($name, $options, $html_options);    
}

/**
 *  Make a new UrlHelper object and call its url_for() method
 *  @uses UrlHelper::url_for()
 */
function url_for($options = array()) {
    $url_helper = new UrlHelper();
    return $url_helper->url_for($options);
}

?>