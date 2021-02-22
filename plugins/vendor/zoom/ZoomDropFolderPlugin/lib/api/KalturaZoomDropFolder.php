<?php

/**
 * @package plugins.vendor.Zoom.ZoomDropFolder
 * @subpackage api.objects
 */
class KalturaZoomDropFolder extends KalturaDropFolder
{
	
	/**
	 * @var string
	 * @readonly
	 */
	public $refreshToken;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $jwtToken;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $zoomVendorIntegrationId;
	
	/**
	 * @var KalturaZoomIntegrationSetting
	 * @readonly
	 */
	public $zoomVendorIntegration;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'zoomVendorIntegrationId',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent ::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent ::doFromObject($sourceObject, $responseProfile);
		
		/* @var ZoomVendorIntegration $vendorIntegration */
		$vendorIntegration = VendorIntegrationPeer ::retrieveByPK($this->zoomVendorIntegrationId);
		
		if($vendorIntegration)
		{
			$zoomIntegrationObject = new KalturaZoomIntegrationSetting();
			$zoomIntegrationObject->fromObject($vendorIntegration);
			$this->zoomVendorIntegration = $zoomIntegrationObject;
			$this->refreshToken = $vendorIntegration ->getRefreshToken();
			$this->jwtToken = $vendorIntegration ->getJwtToken();
		}
		else
		{
			throw new KalturaAPIException(KalturaZoomDropFolderErrors::DROP_FOLDER_INTEGRATION_DATA_MISSING);
		}
		
	}
}