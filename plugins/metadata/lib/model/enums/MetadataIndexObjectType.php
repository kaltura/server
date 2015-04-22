<?php
/**
 * @package plugins.metadata
 * @subpackage model.enum
 */
class MetadataIndexObjectType implements IKalturaPluginEnum, IndexObjectType
{
	const METADATA = 'Metadata';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'METADATA' => self::METADATA,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			MetadataPlugin::getApiValue(self::METADATA) => 'Metadata',
		);
	}
}
