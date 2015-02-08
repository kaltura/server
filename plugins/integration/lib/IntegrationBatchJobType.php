<?php
/**
 * @package plugins.integration
 * @subpackage lib.enum
 */
class IntegrationBatchJobType implements IKalturaPluginEnum, BatchJobType
{
	const INTEGRATION = 'Integration';
	
	public static function getAdditionalValues()
	{
		return array(
			'INTEGRATION' => self::INTEGRATION,
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
