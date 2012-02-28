<?php

error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../infra/kConf.php');

// clear kConf defined cache directories
$path = realpath(kConf::get('cache_root_path'));

askToDelete(fixPath(kConf::get('general_cache_dir')));
askToDelete(fixPath(kConf::get('response_cache_dir')));
askToDelete(fixPath(kConf::get('cache_root_path')));

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
		echo 'Are you sure you want to delete all files under ['.$path.'] (y/n) ?  ';
		$input = trim(fgets(STDIN));
		if ($input === strtolower('y')) {
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$cmd = 'del /F /S /Q '.$path.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'* del /F /S /Q '.$path.DIRECTORY_SEPARATOR.'*.*';
                    } else {
		        $cmd = "find $path -type f -exec rm -rf {} \;";
                    }
                    echo "Executing: $cmd\n";
                    system($cmd,$rc);
                    if ($rc){
                            echo "Failed to clean up $path.";
                    }
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
