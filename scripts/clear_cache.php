<?php

error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../alpha/config/kConf.php');

// clear kConf defined cache directories
system('rm '.realpath(kConf::get('cache_root_path')).DIRECTORY_SEPARATOR.'*');
system('rm '.realpath(kConf::get('general_cache_dir')).DIRECTORY_SEPARATOR.'*');
system('rm '.realpath(kConf::get('response_cache_dir')).DIRECTORY_SEPARATOR.'*');

// clear symfony (alpha) cache
system('php '.realpath(kConf::get('sf_root_dir')).DIRECTORY_SEPARATOR.'symfony cc');
system('php '.realpath(kConf::get('sf_root_dir')).DIRECTORY_SEPARATOR.'symfony cc');

// clear APC cache
if (function_exists('apc_clear_cache'))
{
	// clear apc system cache
	apc_clear_cache(); 
	
	// clear apc user cache
	apc_clear_cache('user');
}