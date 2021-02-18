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
	 * @var string
	 * @readonly
	 */
	public $zoomVendorIntegrationId;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $accountId;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'refreshToken',
		'jwtToken',
		'zoom_vendor_integration_id',
		'accountId',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent ::getMapBetweenObjects(), self ::$map_between_objects);
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent ::doFromObject($sourceObject, $responseProfile);
		
		/* @var ZoomVendorIntegration $vendorIntegration */
		$vendorIntegration = VendorIntegrationPeer ::retrieveByPK($this->zoomVendorIntegrationId);
		$this->refreshToken = $vendorIntegration ->getRefreshToken();
		$this->refreshToken = $vendorIntegration ->getJwtToken();
		$this->refreshToken = $vendorIntegration ->getAccountId();
		
	}
}