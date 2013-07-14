<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage api.objects
 */
class KalturaAttUverseDistributionFile extends KalturaObject
{
	
	/**
	 * @var string
	 */
	public $remoteFilename;
	
	/**
	 * @var string
	 */
	public $localFilePath;
	
	/**
	 * @var KalturaAssetType
	 */
	public $assetType;
	
	/**
	 * @var string
	 */
	public $assetId;

}
