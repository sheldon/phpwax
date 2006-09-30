<?php
/**
 *
 * @package wx.framework.core
 * @author Ross Riley
 **/
class WXTemplate
{
	public $layout_path = null;
	public $view_path;
  public $content_for_layout;
	public $layout_content;
	public $view_content;
	public $preserve_buffer = null;
	
	public function __construct($preserve_buffer = null) {
		if($preserve_buffer) {
			$this->preserve_buffer = true;
		}
	}
	
	public function parse( $pFile ) {
		$this->preserve_buffer ? $buffer = ob_get_clean() : ob_clean();
		ob_start();
		$pFile = VIEW_DIR.$pFile;
		extract((array)$this);
		if(!is_readable($pFile)) {
			throw new WXException("Unable to find ".$pFile, "Missing Template File");
		}
		include( $pFile );
		if($this->preserve_buffer) {
			$content = ob_get_clean();
			ob_start();
			echo $buffer;
			return $content;
		} else {
			return ob_get_clean();
		}
	}
	
	public function setTemplate($file) {
		$this->outer_template=$file;
	}
	
	public function execute() {
		$this->content_for_layout = $this->parse($this->view_path);	
		
		$this->layout_content = $this->content_for_layout;
		if($this->layout_path) {
			return $this->parse($this->layout_path);
		} else {
			return $this->layout_content;
		}
		
	}
	
  

} // END class 
?>