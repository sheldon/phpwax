#!/usr/bin/php
<?php
require_once dirname(__FILE__).'/../app/config/environment.php';
$source_location = "svn://svn.webxpress.com/home/SVN/wxframework/trunk/skel/script"
$output_dir = WAX_ROOT."script";

$command = "svn export {$source_location} {$output_dir} --force";
system($command);
echo "All scripts have been updated to the latest versions.
";
?>