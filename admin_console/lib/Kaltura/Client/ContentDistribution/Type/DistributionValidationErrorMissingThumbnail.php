<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Type_DistributionValidationErrorMissingThumbnail extends Kaltura_Client_ContentDistribution_Type_DistributionValidationError
{
	public function getKalturaObjectType()
	{
		return 'KalturaDistributionValidationErrorMissingThumbnail';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_DistributionThumbDimensions
	 */
	public $dimensions;


}

