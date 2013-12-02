<?

$config=array(
	'hashFile'=>'.hash',
	'dirPermissions'=>0755,
	'comment'=>'//',
	'include'=>'-include',
	'prefixes'=>array(
		'Chrome'=>array('wk','webkit',),
		'Firefox'=>array('ff','moz','firefox'),
		'Internet Explorer'=>array('ms','ie',),
		'Opera'=>array('o','op','opera'),
		'Safari'=>array('wk','sf','safari',),
		),
	'parsers'=>array(
		'less'=>'Less',
		'sass'=>'Sass',
		'scss'=>'Sass',
		),
	'cacheDir'=>'.cache',
	'cacheFiles'=>array(
		'Chrome'=>'wk',
		'Default Browser'=>'_default',
		'Firefox'=>'ff',
		'Explorer'=>'ie',
		'IE'=>'ie',
		'Internet Explorer'=>'ie',
		'Opera'=>'op',
		'Safari'=>'wk',
		),
	);

?>