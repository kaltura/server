<?php
class Kaltura_InfraLoader implements Zend_Loader_Autoloader_Interface
{
	public function Kaltura_InfraLoader()
	{
		$cacheDir = realpath('../cache/');
		$infaDir = realpath('../../infra/');
		$pluginsDir = realpath('../../plugins/');
		
		require_once($infaDir . DIRECTORY_SEPARATOR . 'bootstrap_base.php');
		require_once($infaDir . DIRECTORY_SEPARATOR . 'KAutoloader.php');
		KAutoloader::setClassPath(array($infaDir . DIRECTORY_SEPARATOR . '*'));
		KAutoloader::addClassPath(KAutoloader::buildPath($pluginsDir, '*'));
		KAutoloader::setClassMapFilePath(KAutoloader::buildPath($cacheDir, "KalturaClassMap.cache"));
		KAutoloader::register();
	}
	
	public function autoload($class)
	{
		KAutoloader::autoload($class);
	}
}