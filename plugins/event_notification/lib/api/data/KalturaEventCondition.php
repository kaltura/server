<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaEventCondition extends KalturaObject
{	
	/**
	 * @param string $class class name of the core object
	 * @return KalturaEventCondition
	 */
	public static function getInstanceByClass($class)
	{
		if($class == 'kEventFieldCondition')
			return new KalturaEventFieldCondition();
			
		return KalturaPluginManager::loadObject('KalturaEventCondition', $class);
	}
}