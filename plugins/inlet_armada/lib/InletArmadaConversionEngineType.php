<?php
/**
 * @package api
 * @subpackage enum
 */
class InletArmadaConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const INLET_ARMADA = 'InletArmada';
	
	public static function getAdditionalValues()
	{
		return array(
			'INLET_ARMADA' => self::INLET_ARMADA
		);
	}
}
