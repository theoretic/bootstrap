<?
/*
AT
24.12.13
*/

class Browser
	{

	function __construct()
		{
		$browser=get_browser(null, true);
//debug($browser,'Browser:browser');
		foreach($browser as $name=>$value)
			$this->$name=$value;

		if(!$this->browser || $this->browser=='Default Browser')
			{
//echo $_SERVER['HTTP_USER_AGENT'];//
			//some ugly patches...
			if(strstr($_SERVER['HTTP_USER_AGENT'],'Chrome'))
				{
				$this->browser='Chrome';
				$parts=explode(' ',$_SERVER['HTTP_USER_AGENT']);
				foreach($parts as $part)
					{
					if(!strstr($part,'Chrome')) continue;
					$part=trim($part);
					list($tmp,$fullversion)=explode('/',$part);
					$subparts=explode('.',$fullversion);
					$this->version=$subparts[0];
					break;
					}
				}
			if(strstr($_SERVER['HTTP_USER_AGENT'],'Firefox'))
				{
				$parts=explode(' ',$_SERVER['HTTP_USER_AGENT']);
				$nameversion=array_pop($parts);
				list($this->browser,$this->version)=explode('/',$nameversion);
				}
			if(strstr($_SERVER['HTTP_USER_AGENT'],'MSIE'))
				{
				$this->browser='IE';
				$parts=explode(';',$_SERVER['HTTP_USER_AGENT']);
				foreach($parts as $part)
					{
					if(!strstr($part,'MSIE')) continue;
					$part=trim($part);
					list($tmp,$this->version)=explode(' ',$part);
//echo "--$part--{$this->version}--";//
					break;
					}
				}
			}

//debug($this,'Browser');
		$this->name=$this->browser;
		$this->version=(int)$this->version;
//echo "--{$this->browser}--{$this->version}--";//
		}

	}

?>