<?php

/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.objects
 */
class KalturaWebexAPIDropFolder extends KalturaDropFolder
{
	const WEBEX_BASE_URL = 'WebexBaseUrl';
	
	/**
	 * @readonly
	 */
	public $refreshToken;
	
	/**
	 * @readonly
	 */
	public $accessToken;

	/**
	 * @readonly
	 */
	public $accessExpiresIn;

	/**
	 * @readonly
	 */
	public $clientId;
	
	/**
	 * @readonly
	 */
	public $clientSecret;
	
	/**
	 * @readonly
	 */
	public $baseURL;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $webexAPIVendorIntegrationId;
	
	/**
	 * @var KalturaWebexAPIIntegrationSetting
	 * @readonly
	 */
	public $webexAPIVendorIntegration;
	
	/**
	 * @var time
	 */
	public $lastHandledMeetingTime;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'webexAPIVendorIntegrationId',
		'lastHandledMeetingTime'
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($sourceObject, $responseProfile);
		
		/* @var WebexAPIVendorIntegration $vendorIntegration */
		$vendorIntegration = VendorIntegrationPeer::retrieveByPK($this->webexAPIVendorIntegrationId);
		try
		{
			if ($vendorIntegration)
			{
//				$headerData = self ::getZoomHeaderData();
//				$this -> clientId = $headerData[0];
//				$this -> clientSecret = $headerData[1];
//				$this -> baseURL = $headerData[2];
//				$this -> jwtToken = $vendorIntegration -> getJwtToken();
//				$this -> refreshToken = $vendorIntegration -> getRefreshToken();
//				$this -> accessToken = $vendorIntegration -> getAccessToken();
//				$this -> description = $vendorIntegration->getZoomAccountDescription();
//				$this -> accessExpiresIn = $vendorIntegration->getExpiresIn();
				$webexAPIClient = new kWebexAPIClient();
				
//				if ($this -> accessToken && $this -> refreshToken && kCurrentContext ::$ks_partner_id == Partner::BATCH_PARTNER_ID &&
//					$vendorIntegration -> getExpiresIn() <= time() +
//					kconf ::getArrayValue('tokenExpiryGrace', 'ZoomAccount', 'vendor', 600))
//				{
//					KalturaLog ::debug('Token expired for account id: ' . $vendorIntegration -> getAccountId() . ' renewing with the new tokens');
//					$freshTokens = $zoomClient -> refreshTokens();
//					if ($freshTokens)
//					{
//						$this -> accessToken = $freshTokens[kZoomTokens::ACCESS_TOKEN];
//						$this -> refreshToken = $freshTokens[kZoomTokens::REFRESH_TOKEN];
//						$this -> accessExpiresIn = $freshTokens[kZoomTokens::EXPIRES_IN];
//						$vendorIntegration -> saveTokensData($freshTokens);
//					}
//				}
				
				
				$webexAPIIntegrationObject = new KalturaWebexAPIIntegrationSetting();
				$webexAPIIntegrationObject->fromObject($vendorIntegration);
				$this->webexAPIVendorIntegration = $webexAPIIntegrationObject;
			}
			else
			{
				//throw new KalturaAPIException(KalturaZoomDropFolderErrors::DROP_FOLDER_INTEGRATION_DATA_MISSING);
			}
		}
		catch (Exception $e)
		{
			$this->errorDescription = $e->getMessage();
		}
		
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if ($this->description)
		{
			/* @var WebexAPIVendorIntegration $vendorIntegration */
			$vendorIntegration = VendorIntegrationPeer::retrieveByPK($dbObject->getWebexAPIVendorIntegrationId());
			if (!$vendorIntegration)
			{
				//throw new KalturaAPIException(KalturaZoomDropFolderErrors::DROP_FOLDER_INTEGRATION_DATA_MISSING);
			}
			$vendorIntegration->save();
		}
		
		if (!$dbObject)
		{
			$dbObject = new WebexAPIDropFolder();
		}
		
		$dbObject->setType(WebexAPIDropFolderPlugin::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API));
		return parent::toObject($dbObject, $skip);
	}
}