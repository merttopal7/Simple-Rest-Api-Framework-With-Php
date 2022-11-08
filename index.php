<?php
$cors=function(array$options){foreach($options as$key => $option) {if($key=='origin')header("Access-Control-Allow-Origin: ".$option);
if($key=='headers')header("Access-Control-Allow-Headers: ".$option);}};spl_autoload_register('autoLoader');
function autoLoader($className){$path="system/";$extension=".tmr.php";$fullPath=$path.$className.$extension;
return(!file_exists($fullPath))?false:include_once$fullPath;}$controller=function(array$paths)
{foreach($paths as$path){include_once'app/controller/'.$path.'.php';}};$model=function(array$paths){foreach($paths as$path)
{include_once'app/model/'.$path.'.model.php';}};$model(['queryBuilder']);$app=new app();include_once'./routes/app.php';
?>