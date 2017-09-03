<?php
/**
 * @package plugins.beacon
 * @subpackage model.enum
 */ 
class ClearBeaconsBatchType implements IKalturaPluginEnum, BatchJobType
{
	const CLEAR_BEACONS = 'ClearBeacons';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'CLEAR_BEACONS' => self::CLEAR_BEACONS,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array();
	}

}
