<?

function debug($_input,$_caption,$_backtrace=0)
	{
	ob_start();
//	echo "[debug]".__FILE__." @ ".__LINE__. " $_caption :<pre>";
	echo "<div class=pre>[debug] $_caption :<br>";
	//var_dump($_input);
	print_r($_input);

	if($_backtrace)
		{
		//var_dump(debug_backtrace());
		$data=debug_backtrace();
		echo "--file {$data[file]}";
		echo "--line {$data[line]}";
		}
	echo "</div>";

	$output=ob_get_clean();
	$output=str_replace('\n','<br>',$output);
	$output=str_replace('[','<br>&nbsp;[',$output);
	echo $output;
	}

?>