<?php

/**
 * @package plugins.interactivity
 * @subpackage lib.enum
 */

class InteractivityEntryCapability implements IKalturaPluginEnum, EntryCapability
{
	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array(
			'KALTURA_INTERACTIVITY_CAPABILITY_NAME' => InteractivityPlugin::PLUGIN_NAME
		);
	}

	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}

}