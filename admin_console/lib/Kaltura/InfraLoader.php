<?php
class Kaltura_InfraLoader implements Zend_Loader_Autoloader_Interface
{
	public function autoload($class)
	{
		$infaDir = realpath('../../infra/');
		//$pluginsDir = realpath('../../plugins/');
		
		require_once($infaDir . DIRECTORY_SEPARATOR . 'KAutoloader.php');
		KAutoloader::setClassPath(array($infaDir . DIRECTORY_SEPARATOR . '*'));
		//KAutoloader::addClassPath(KAutoloader::buildPath($pluginsDir, "*"));
		KAutoloader::setNoCache(true);
		KAutoloader::autoload($class);
	}
}