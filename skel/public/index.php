<?php
define('WAX_ROOT', (dirname(dirname(__FILE__)))."/" );
define('CACHE_DIR', WAX_ROOT.'tmp/cache/');
define('PUBLIC_DIR', WAX_ROOT.'public_html/');
require_once WAX_ROOT.'wax/utilities/Session.php';
Session::start();

$use_cache = false; //set to false by default
$cache_time = 3600; //length of time to cache by
$mtime = 0; //file modified time
$cache_file = CACHE_DIR.str_replace("-", "_",$_SERVER['HTTP_HOST']).md5($_SERVER['REQUEST_URI'].serialize($_GET).serialize($_SESSION)) .".layout.cache";

if(is_readable($cache_file)) $mtime = filemtime($cache_file);
$diff = time() - $mtime;
if(is_readable($cache_file) && $diff < $cache_time)
//so if any data has been posted or in the admin area dont use cache
if(count($_POST) || substr_count($_SERVER['REQUEST_URI'], 'admin') || $_REQUEST['no_cache'] == 'y') $use_cache = false;
if(count($_POST) || $_REQUEST['no_cache'] == 'y'){
	foreach(glob(CACHE_DIR.str_replace("-", "_",$_SERVER['HTTP_HOST'])."*") as $file) unlink($file);
}

if($use_cache){	
	echo file_get_contents($cache_file);
	exit;
}else{
	require_once dirname(__FILE__).'/../app/config/environment.php';
	AutoLoader::run_application();
}
?>