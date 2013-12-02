<?
/*
AT
12.11.13
*/

class Script
	{
	var
		$config,
		$path,
		$files,
		$cacheDir,
		$cacheFile,
		$hash,
		$hashFile,
		$savedHash,
		$js
		;

	function __construct($_path)
		{
		if(substr($_path,-1)=='/') $_path=substr($_path,0,strlen($_path)-1);
		$this->path=$_path;
		return true;
		}

	function make()
		{
		$this->setCacheFile();
		$this->setHashFile();
		$this->getSavedHash();

		$this->getFiles();
		$this->merge();
		$this->makeHash();

		if(!$this->isActual())
			{
			$this->minify();
			$this->saveCache();
			$this->saveHash();
			}

		return true;
		}

	//files

	private function getFiles()
		{
		$this->files=glob($this->path.'/*.js');
		natcasesort($this->files);
		return $this->files;
		}

	//hash

	private function setHashFile()
		{
		$this->hashFile="{$this->path}/{$this->config[cacheDir]}/{$this->config[hashFile]}";
		return $this->hashFile;
		}

	private function makeHash()
		{
		//foreach($this->files as $file) $js.=file_get_contents($file);
		//$this->hash=md5($js);
		$this->hash=md5($this->js);
		return $this->hash;
		}

	private function getSavedHash()
		{
		$this->savedHash=file_get_contents($this->hashFile);
		return $this->savedHash;
		}

	private function saveHash()
		{
		$fp=fopen($this->hashFile,'w+');
		fwrite($fp,$this->hash);
		fclose($fp);
		return true;
		}

	private function isActual()
		{
		$actual=$this->hash == $this->savedHash;
		if($actual && !is_file($this->cacheFile)) $actual=false;
		return $actual;
		}


	private function merge()
		{
		$this->js='';
		foreach($this->files as $i=>$file)
			{
//echo "parse(): file --$file--<br>";//
			$this->js.=file_get_contents($file);
			}
		return $this->js;
		}

	function minify()
		{
		$JSMin=new JSMin();
		$this->js=$JSMin::minify($this->js);
		//removing multiline comments
		$this->js=preg_replace('!/\*.*?\*/!s','',$this->js);
		return $this->js;
		}

	//cache

	private function setCacheFile()
		{
		$dir=$this->path.'/'.$this->config['cacheDir'];

		if(!is_dir($dir)) mkdir($dir,$this->config['dirPermissions']);

		$file="$dir/{$this->config[cacheFile]}";
		$this->cacheFile=$file;
		return $file;
		}

	private function saveCache()
		{
		$fp=fopen($this->cacheFile,'w+');
		fwrite($fp,$this->js);
		fclose($fp);
		return true;
		}

	function output()
		{
		$js=($this->js)? $this->js : file_get_contents($this->cacheFile);
		header('Content-Type: text/javascript');
		header('ETag: '.md5($js));
		echo $js;
		}
	}

?>