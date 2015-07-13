<?php

/**
 * @package plugins.queue
 */
class QueuePlugin extends KalturaPlugin implements IKalturaVersion, IKalturaRequire
{
	const PLUGIN_NAME = 'queue';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);		
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaRequire::requires()
	 */	
	public static function requires()
	{
	    return array("IKalturaQueuePlugin");
	}
}
