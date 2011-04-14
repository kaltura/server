<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_ExtractMediaJobData extends Kaltura_Client_Type_ConvartableJobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaExtractMediaJobData';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;


}

