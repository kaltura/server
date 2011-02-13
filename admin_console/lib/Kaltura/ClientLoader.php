<?php
class Kaltura_ClientLoader implements Zend_Loader_Autoloader_Interface
{
	public function autoload($class)
	{
		if (strpos($class, 'Kaltura') === 0)
		{
			require_once(APPLICATION_PATH . '/lib/Kaltura/KalturaClient.php');
		
			// load all plugins
			$pluginsFolder = APPLICATION_PATH . '/lib/Kaltura/KalturaPlugins';
			if(!is_dir($pluginsFolder))
				return;
				
			$dir = dir($pluginsFolder);
			while (false !== $fileName = $dir->read())
				if(preg_match('/^Kaltura([^.]+)Plugin.php$/', $fileName, $matches))
					require_once("$pluginsFolder/$fileName");
		}
	}
}