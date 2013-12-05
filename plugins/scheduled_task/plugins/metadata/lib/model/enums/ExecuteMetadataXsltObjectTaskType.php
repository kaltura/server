<?php

/**
 * @package plugins.scheduledTaskMetadata
 * @subpackage model.enum
 */
class ExecuteMetadataXsltObjectTaskType implements IKalturaPluginEnum, ObjectTaskType
{
	const EXECUTE_METADATA_XSLT = 'ExecuteMetadataXslt';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'EXECUTE_METADATA_XSLT' => self::EXECUTE_METADATA_XSLT,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			ScheduledTaskMetadataPlugin::getApiValue(self::EXECUTE_METADATA_XSLT) => 'Dispatch event notification',
		);
	}
}
