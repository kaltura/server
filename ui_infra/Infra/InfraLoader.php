<?php
/**
 * @package UI-infra
 * @subpackage bootstrap
 */
require_once __DIR__ . '/../../infra/kEnvironment.php';

/**
 * @package UI-infra
 * @subpackage bootstrap
 */
class Infra_InfraLoader implements Zend_Loader_Autoloader_Interface
{
	public function __construct(Zend_Config $config = null)
	{
		$infaFolder = null;
		$pluginsFolder = null;
		$cachePath = null;
		if($config)
		{
			if(isset($config->cachePath))
				$cachePath = $config->cachePath;
			if(isset($config->infaFolder))
				$infaFolder = $config->infaFolder;
			if(isset($config->pluginsFolder))
				$pluginsFolder = $config->pluginsFolder;
		}
		
		if(!$infaFolder)
			$infaFolder = realpath(dirname(__FILE__) . '/../../infra/');
		if(!$pluginsFolder)
			$pluginsFolder = realpath(dirname(__FILE__) . '/../../plugins/');
		if(!$cachePath)
			$cachePath = kEnvironment::get("cache_root_path") . '/infra/classMap.cache';
		
		require_once($infaFolder . DIRECTORY_SEPARATOR . 'KAutoloader.php');
		require_once($infaFolder . DIRECTORY_SEPARATOR . 'kEnvironment.php');
		
			
		KAutoloader::setClassPath(array($infaFolder . DIRECTORY_SEPARATOR . '*'));
		KAutoloader::addClassPath(KAutoloader::buildPath($pluginsFolder, '*'));
		KAutoloader::setClassMapFilePath($cachePath);
		KAutoloader::register();
	}
	
	public function autoload($class)
	{
		KAutoloader::autoload($class);
	}
}
