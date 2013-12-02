<?
/*
AT
21.11.13

include syntax:

-include path/to/file

prefix syntax:

-webkit-
-webkit7-
-!o5-
-moz6>-
-ms>6-
-ie<8-
-ie8>-
-!ie8>-



*/

class Style
	{
	var
		$config,
		$Browser,
		$path,
		$files,
		$cacheDir,
		$cacheFile,
		$hash,
		$hashFile,
		$savedHash,
		$chunks,
		$css
		;

	function __construct($_path)
		{
		if(substr($_path,-1)=='/') $_path=substr($_path,0,strlen($_path)-1);
		$this->path=$_path;
		$this->Browser=new Browser();

		return true;
		}

	function make()
		{
		$this->setCacheFile();
		$this->setHashFile();
		$this->getSavedHash();

		$this->getFiles();
		$this->makeHash();

		if(!$this->isActual())
			{
			$this->chunkify();
			$this->parseChunks();
			$this->minify();
			$this->saveCache();
			$this->saveHash();
			}

		return true;
		}

	//files

	private function getFiles()
		{
		//pass 1
		$this->files=glob($this->path.'/*.*ss');
		//sort($this->files);
//debug($this->files,'getFiles():this->files');//

		//pass2: looking for included files
		$this->includeFiles();

		return $this->files;
		}

	//includes

	private function includeFiles()
		{
		// any file may contain line like -include path/to/another/file.ext . These files should be placed into $this->files array

		$files=$this->files;
		foreach($files as $i=>$file)
			{
			$lines=file($file);
			unset($includeFiles);
			foreach($lines as $j=>$line)
				{
				if(!$includeFile=$this->findInclude($line)) continue;
//echo "includeFiles(): -$i- -$j- -$line-<br>";//
				//$files=$this->insertFile($files,$includeFile,$i);
				$includeFiles[]=$includeFile;
				}
			$files=$this->mergeIncludeFiles($files,$includeFiles,$i);
			}
		$this->files=$files;
//debug($this->files,'includeFiles():this->files');//
		return $this->files;
		}

	private function findInclude($_line)
		{
		if(strpos($_line,$this->config['include'])!==0) return false;
		$_line=preg_replace('/\s+/s',' ',$_line);
		$_line=$this->stripSingleLineComment($_line);
		list($tmp,$includeFile)=explode(' ',$_line);
//debug("findInclude($_line) //".$this->config['include']."// : -$pos- includeFile -$includeFile-");//
		return $includeFile;
		}

	private function mergeIncludeFiles($_files,$_includeFiles,$_i)
		{
		if(sizeof($_includeFiles)<1) return $_files;

		$filesBegin=array_slice($_files,0,$_i);
		$filesEnd=array_slice($_files,$_i);
//debug($filesBegin,"mergeIncludeFiles($_includeFiles,$_includeFiles,$_i): filesBegin");//
//debug($filesEnd,"mergeIncludeFiles($_includeFiles,$_includeFiles,$_i): filesEnd");//
		$files=array_merge($filesBegin,$_includeFiles,$filesEnd);
//debug($files,"mergeIncludeFiles($_includeFiles,$_includeFiles,$_i): merged");//
		return $files;
		}

	//hash

	private function setHashFile()
		{
		$this->hashFile="{$this->path}/{$this->config[cacheDir]}/{$this->config[hashFile]}";
		return $this->hashFile;
		}

	private function makeHash()
		{
		foreach($this->files as $file) $css.=file_get_contents($file);
		$this->hash=md5($css);
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

	//prefixes

	private function getPrefix($_line)
		{
		$_line=trim($_line);
		if(substr($_line,0,1)!='-') return false;
		$parts=explode('-',$_line);
		return $parts[1];
		}

	private function parsePrefix($_prefix)
		{
		$prefix['negation']=strstr($_prefix,"!");

		$prefix['abbr']=$_prefix;
		$prefix['abbr']=preg_replace('/([\!0-9\<>])+/','',$prefix['abbr']);

		$prefix['version']=$_prefix;
		$prefix['version']=preg_replace('/([\!a-z<>])+/','',$prefix['version']);
		$prefix['version']=strlen($prefix['version'])? $prefix['version']:false;

		$prefix['postfix']=$_prefix;
		$prefix['postfix']=preg_replace('/([\!a-z0-9])+/','',$prefix['postfix']);
		$prefix['postfix']=strlen($prefix['postfix'])? $prefix['postfix']:false;

		return $prefix;
		}

	private function abbr2names($_abbr)
		{
		$names=array();
		foreach($this->config['prefixes'] as $name=>$abbrs)
			{
			if(in_array($_abbr,$abbrs))
				$names[]=$name;
			}
		return $names;
		}

	private function stripPrefix($_line)
		{
		//-someprefix-   \t  \t  some-css-code -> some-css-code

//echo "stripPrefix($_line)<br>";//
		$_line=trim($_line);
		$_line=str_replace('\t',' ',$_line);
		$parts=explode(' ',$_line);
		$possiblePrefix=$parts[0];
		if(substr($possiblePrefix,0,1)!='-' || substr($possiblePrefix,-1)!='-') return $_line;

		unset($parts[0]);
		$result=implode(' ',$parts);
//echo "stripPrefix($_line) -> --$result--<br>";//
		return implode(' ',$parts);
		}

	//parse

	private function getParserName($_file)
		{
		$extension=array_pop(explode('.',$_file));
		//ignoring the 'x' part of extension, i.e. xless should be parsed as less
		if(substr($extension,0,1)=='x') $extension=substr($extension,1);
		return $this->config['parsers'][$extension];
		}

	private function chunkify()
		{
		// (file01.css, file02.xless, file03.css, file04.xless) => chunks ('Less'=>(file02 lines, file04 lines), 'css'=>(file01 lines, file03 lines))

		$this->chunks=array();
		foreach($this->files as $i=>$file)
			{
			$chunkName=$this->getParserName($file);
			if(!$chunkName) $chunkName='css';//no need to use specific parser
//echo "chunkify(): file --$file-- chunkName --$chunkName--<br>";//

			$lines=array();
			foreach(file($file) as $i=>$line)
				{
				$line=$this->parseLine($line);
				if($line=='')
					{
					unset($lines[$i]);
					continue;
					}
//echo "chunkify(): file -$file- line -$line-<br>";//
				$lines[$i]=$line;
				}

			//$this->chunks[$chunkName].=implode('',$lines); //possible parsing errors when single-line comments like // are used not at the beginning of the line!
			$this->chunks[$chunkName].=implode(PHP_EOL,$lines);
			}
//debug($this->chunks,'chunkify(): chunks');//
		return $this->chunks;
		}

	private function parseChunks()
		{
		$this->css='';
		foreach($this->chunks as $name=>$chunk)
			{
			if($name=='css') //no need to parse
				{
				$this->css.=$chunk;
				continue;
				}

			//not pure css: using specific parser

			unset($Parser);
			$Parser=new $name($chunk);
			$Parser->parse();
			$this->css.=$Parser->css;
			}
		return $this->css;
		}

	private function parseLine($_line)
		{
		if(!$_line || $_line=='') return $_line;

		$_line=trim($_line);

		//pass 1: unsetting commented lines
		//if(strpos($_line,$this->config['comment'])===0) return '';
		$_line=$this->stripSingleLineComment($_line);

		//pass 2: unsetting lines with -import command
		if(strpos($_line,$this->config['import'])===0) return '';

		//pass 3: checking whether the line fits current browser
		$prefix=$this->parsePrefix($this->getPrefix($_line));
		if(!$this->fitsBrowser($prefix)) return '';

		//pass4: stripping browser prefix if placed at the beginning of the line
		$_line=$this->stripPrefix($_line);
//echo "--$_line--<br>";//
		return $_line;
		}

	private function stripSingleLineComment($_line)
		{
		if(strpos($_line,$this->config['comment'])===false) return $_line;

		$parts=explode($this->config['comment'],$_line);
		return $parts[0];
		}

	function minify()
		{
		//stripping unused spaces and newlines
		$this->css=preg_replace('/\s+/s',' ',$this->css);
		//stripping unused spaces near []{}();:,>+
		$this->css=preg_replace('/( *)([\[\]{}();:,>+])( *)/','$2',$this->css);
		//stripping multiline comnments
		//$this->css=preg_replace('/\/\*[^\*]+\*\//','',$this->css);
		$this->css=preg_replace('!/\*.*?\*/!s','',$this->css);

		$this->css=str_replace(';}','}',$this->css);
		return $this->css;
		}

	private function fitsBrowser($_prefix)
		{
		if(!$_prefix['abbr']) return true;

		$fits=true;
		if(!in_array($this->Browser->name,$this->abbr2names($_prefix['abbr'])))
			$fits=false;
		else//browser name match
			{
			if( $_prefix['version'] && $_prefix['version']!=$this->Browser->version )
				{
				if(!$_prefix['postfix'])
					$fits=false;
				else
					{
					if( $_prefix['postfix']=='>' && $this->Browser->version >= $_prefix['version'] )
						$fits=true;
					if( $_prefix['postfix']=='<' && $this->Browser->version <= $_prefix['version'] )
						$fits=true;
					}
				}
			}

		if($_prefix['negation']!='') $fits=!$fits;

		return $fits;
		}

	//cache

	private function setCacheFile()
		{
		$dir=$this->path.'/'.$this->config['cacheDir'];

		if(!is_dir($dir)) mkdir($dir,$this->config['dirPermissions']);

		$file='_default';
		if($this->Browser->name)
			{
			$file=$this->config['cacheFiles'][$this->Browser->name];
			if($this->Browser->version) $file.=$this->Browser->version;
			}
		$file="$dir/$file.css";
		$this->cacheFile=$file;
		return $file;
		}

	private function saveCache()
		{
		$fp=fopen($this->cacheFile,'w+');
		fwrite($fp,$this->css);
		fclose($fp);
		return true;
		}

	function output()
		{
		$css=($this->css)? $this->css : file_get_contents($this->cacheFile);
		header('Content-Type: text/css');
		header('ETag: '.md5($css));
		echo $css;
		}
	}

?>