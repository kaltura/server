<?php

class KObjectFilterEngineFactory
{
	/**
	 * @param $type
	 * @return KObjectFilterEngineBase
	 */
	public static function getInstanceByType($type)
	{
		switch($type)
		{
			case ObjectFilterEngineType::ENTRY:
				return new KObjectFilterBaseEntryEngine();
			default:
				return KalturaPluginManager::loadObject('KObjectFilterEngineBase', $type);
		}
	}
} 