<?
/*
AT
02.12.13
*/

error_reporting(0);

include_once '_include/__autoload.php';
include_once '_include/config.php';
include_once '_include/debug.php';

$Style=new Style($_REQUEST['request']);

$Style->config=$config;
$Style->make();
$Style->output();

?>