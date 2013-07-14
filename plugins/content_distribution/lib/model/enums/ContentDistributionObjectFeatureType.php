<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
class ContentDistributionObjectFeatureType implements IKalturaPluginEnum, ObjectFeatureType
{
	const CONTENT_DISTRIBUTION = 'ContentDistribution';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() 
	{
		return array
		(
			'CONTENT_DISTRIBUTION' => self::CONTENT_DISTRIBUTION,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}