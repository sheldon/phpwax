#!/usr/bin/php
<?php
$modeldir = dirname(__FILE__).'/../app/model/';
$model_name = ucfirst($argv[1])."Email";
$content = "<?php
class {$model_name} extends WXEmail
{
	
}
?>
";
if(is_readable($modeldir.$model_name.".php")) {
  exit("[ERROR] Not written, a model of that name already exists.
");
}
$command = "echo ".'"'.$content.'"'." > ".$modeldir.$model_name.".php";
system($command);
echo "Email class created in app/model.
";

?>