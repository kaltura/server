<?php
/**
 * @package plugins.playReady
 * @subpackage model.enum
 */
class PlayReadyAccessControlActionType implements IKalturaPluginEnum, RuleActionType
{
	const DRM_POLICY = 'DRM_POLICY';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'DRM_POLICY' => self::DRM_POLICY,
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