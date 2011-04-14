<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Type_DistributionValidationErrorInvalidMetadata extends Kaltura_Client_ContentDistribution_Type_DistributionValidationErrorInvalidData
{
	public function getKalturaObjectType()
	{
		return 'KalturaDistributionValidationErrorInvalidMetadata';
	}
	
	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;


}

