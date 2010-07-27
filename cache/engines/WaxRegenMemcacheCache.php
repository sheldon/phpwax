<?
class WaxRegenMemcacheCache{
	
	public function __construct($meta_key, $server, $port){
		$this->memcache = new Memcache;
		$this->memcache->connect($server, $port) or $this->memcache = false;
		
		$config = unserialize($this->memcache->get($meta_key));
		$post = unserialize($config['post']);
		$url = $this->parse_location($config['location']);
		if(count($post)==0 && ($content = $this->curl($url) ) ){
			$this->memcache->replace($config['ident'], $content."<!--testing-->", false, 0);
			$config['time'] = time();
			$config['regen'] = date("Y-m-d H:i:s");			
			$this->memcache->replace($meta_key, serialize($config), false, 0);
		}
	}
	
	public function curl($url){
    $headers = array(	"Content-Type: application/html; charset=UTF-8", "Accept: application/html; charset=UTF-8");
		$session = curl_init($url);
		curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($session, CURLOPT_USERAGENT, API_USER_AGENT);		
		$exec =  curl_exec($session);		
		$info = curl_getInfo($session);
		if($info['http_code'] == 200) return $exec;
		return false;
  }
	
	public function parse_location($url){
		$parsed = parse_url($url);
		return $parsed['scheme']."://".$parsed['host'].$parsed['path']."?no-wax-cache=1&".$parsed['query'];
	}
}


if(isset($argv)){
	 $cache = new WaxRegenFileCache($argv[1], $argv[2], $argv[3]);
}

?>