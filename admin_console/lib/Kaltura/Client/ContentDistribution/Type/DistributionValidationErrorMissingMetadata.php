<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Type_DistributionValidationErrorMissingMetadata extends Kaltura_Client_ContentDistribution_Type_DistributionValidationError
{
	public function getKalturaObjectType()
	{
		return 'KalturaDistributionValidationErrorMissingMetadata';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $fieldName = null;


}

