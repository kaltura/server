<?php
/**
 * @package plugins.visualRecognition
 * @subpackage lib.enum
 */
class VisualRecognitionProviderType implements IKalturaPluginEnum, IntegrationProviderType
{
	const VISUAL_RECOGNITION = 'VisualRecognition';
	
	public static function getAdditionalValues()
	{
		return array(
			'VISUAL_RECOGNITION' => self::VISUAL_RECOGNITION,
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
