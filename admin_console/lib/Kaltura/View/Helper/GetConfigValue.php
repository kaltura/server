<?php
class Kaltura_View_Helper_GetConfigValue extends Zend_View_Helper_Abstract
{
	public function getConfigValue($name, $defaultValue = null, $section = 'settings')
	{
		$config = Zend_Registry::get('config')->$section;
		if(!$config || !isset($config->$name))
			return $defaultValue;
		
		return $config->$name;
	}
}