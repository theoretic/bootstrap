<?

function __autoload($_class)
	{
	include_once '_include/classes/'.$_class.'.php';
	}

?>