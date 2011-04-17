<?php

error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../alpha/config/kConf.php');

// clear kConf defined cache directories
$path = realpath(kConf::get('cache_root_path'));

askToDelete(fixPath(kConf::get('general_cache_dir')));
askToDelete(fixPath(kConf::get('response_cache_dir')));
askToDelete(fixPath(kConf::get('cache_root_path')));

// clear symfony (alpha) cache
system('php '.fixPath(kConf::get('sf_root_dir')).DIRECTORY_SEPARATOR.'symfony cc');
system('php '.fixPath(kConf::get('sf_root_dir')).DIRECTORY_SEPARATOR.'symfony cc');

// clear APC cache
if (function_exists('apc_clear_cache'))
{
	// clear apc system cache
	if (!apc_clear_cache())
	{
		echo 'Unable to clear APC SYSTEM cache!'.PHP_EOL;
	} 
	
	// clear apc user cache
	if (!apc_clear_cache('user'))
	{
		echo 'Unable to clear APC USER cache!'.PHP_EOL;
	}
}

function fixPath($path)
{
	$path = str_replace('\\', '/', $path);
	return realpath($path);
}


function askToDelete($path)
{	
	$baseKalturaPath = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..');
	if (strpos($path, $baseKalturaPath) === 0)
	{
		echo 'Are you sure you want to delete all contents of ['.$path.'] (y/n) ?  ';
		$input = trim(fgets(STDIN));
		if ($input === 'y') {
			$cmd = 'rm -rf '.$path.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'*';
			echo "$cmd\n";
			system($cmd);
			
			$cmd = 'rm -rf '.$path.DIRECTORY_SEPARATOR.'*.*';
			echo "$cmd\n";
			system($cmd);
		}
		else {
			echo 'Skipping...'.PHP_EOL;
		}
	}
	else
	{
		echo 'Path ['.$path.'] does not belong to the kaltura server. Skipping.'.PHP_EOL;
	}	
}