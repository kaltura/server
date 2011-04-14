<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_AdminConsole_Type_InvestigateThumbAssetData extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaInvestigateThumbAssetData';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_ThumbAsset
	 * @readonly
	 */
	public $thumbAsset;

	/**
	 * 
	 *
	 * @var Kaltura_Client_FileSync_Type_FileSyncListResponse
	 * @readonly
	 */
	public $fileSyncs;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_ThumbParams
	 * @readonly
	 */
	public $thumbParams;

	/**
	 * 
	 *
	 * @var Kaltura_Client_AdminConsole_Type_ThumbParamsOutputListResponse
	 * @readonly
	 */
	public $thumbParamsOutputs;


}

