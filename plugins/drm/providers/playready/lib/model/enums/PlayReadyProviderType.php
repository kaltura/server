<?php
/**
 * @package plugins.playReady
 * @subpackage model.enum
 */
class PlayReadyProviderType implements IKalturaPluginEnum, DrmProviderType
{
	const PLAY_READY = 'PLAY_READY';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'PLAY_READY' => self::PLAY_READY,
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