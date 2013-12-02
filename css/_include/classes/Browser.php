<?
/*
AT
02.12.13
*/

class Browser
	{

	function __construct()
		{
		$browser=get_browser();

		foreach($browser as $name=>$value)
			$this->$name=$value;

		if(!$this->browser || $this->browser=='Default Browser')
			{
			//some ugly patches...
			if(strstr($_SERVER['HTTP_USER_AGENT'],'Firefox'))
				{
				$parts=explode(' ',$_SERVER['HTTP_USER_AGENT']);
				$nameversion=array_pop($parts);
				list($this->browser,$this->version)=explode('/',$nameversion);
				}
			}

//debug($this,'Browser');
		$this->name=$this->browser;
		$this->version=(int)$this->version;
		}

	}

?>