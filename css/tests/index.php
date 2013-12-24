<?
error_reporting(7);

include_once '_include/__autoload.php';
include_once '_include/config.php';
include_once '_include/debug.php';

//$Style=new Style($_REQUEST['request']);
//$Style->config=$config;

$Browser=new Browser();

debug($Browser->browser,'Browser->browser');
debug($Browser->version,'Browser->version');

//$Style->make();
//$Style->output();


?>