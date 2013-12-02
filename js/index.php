<?
/*
AT
12.11.13
*/

error_reporting(0);

include_once '_include/__autoload.php';
include_once '_include/config.php';
include_once '_include/debug.php';

$Script=new Script($_REQUEST['request']);

$Script->config=$config;
$Script->make();
$Script->output();


?>