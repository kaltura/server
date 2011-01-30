<?php

error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../alpha/config/kConf.php');

// clear kConf defined cache directories
system('rm -rf '.kConf::get('cache_root_path').'/*');
system('rm -rf '.kConf::get('general_cache_dir').'/*');
system('rm -rf '.kConf::get('response_cache_dir').'/*');

// clear symfony (alpha) cache
system(kConf::get('sf_root_dir').'/symfony cc');
system(kConf::get('sf_root_dir').'/symfony cc');

// clear APC cache
if (function_exists('apc_clear_cache'))
{
	// clear apc system cache
	apc_clear_cache(); 
	
	// clear apc user cache
	apc_clear_cache('user');
}