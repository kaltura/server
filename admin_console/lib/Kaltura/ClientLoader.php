<?php
class Kaltura_ClientLoader implements Zend_Loader_Autoloader_Interface
{
	public function autoload($class)
	{
		if (strpos($class, 'Kaltura') === 0)
		{
			require_once(APPLICATION_PATH . '/lib/Kaltura/KalturaClient.php');
		}
	}
}