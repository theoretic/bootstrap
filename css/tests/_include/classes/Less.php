<?
/*
*/

class Less extends Parser
	{
	var
		$includes=array('_include/less/lessc.php',)
		;

	function parse()
		{
		$Lessc=new lessc;
		$this->css=$Lessc->parse($this->css);
		}
	}

?>