<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Type_GenericDistributionProfile extends Kaltura_Client_ContentDistribution_Type_DistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaGenericDistributionProfile';
	}
	
	/**
	 * 
	 *
	 * @var int
	 * @insertonly
	 */
	public $genericProviderId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $submitAction;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $updateAction;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $deleteAction;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_GenericDistributionProfileAction
	 */
	public $fetchReportAction;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredEntryFields = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredMetadataXPaths = null;


}

