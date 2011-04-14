<?php
/**
 * @package Admin
 * @subpackage Client
 */
abstract class Kaltura_Client_Type_AssetParamsOutputBaseFilter extends Kaltura_Client_Type_AssetParamsFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaAssetParamsOutputBaseFilter';
	}
	
	/**
	 * 
	 *
	 * @var int
	 */
	public $assetParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsVersionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetVersionEqual = null;


}

