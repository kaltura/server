<?php

/**
 * @package plugins.ZoomDropFolder
 * @subpackage api.objects
 */
class KalturaZoomDropFolder extends KalturaDropFolder
{
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';
	const MAP_NAME = 'vendor';
	const ZOOM_BASE_URL = 'ZoomBaseUrl';
	
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
	 * @readonly
	 */
	public $accountId;

	/**
	 * @readonly
	 */
	public $zoomAuthType;

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
	
	/**
	 * @var time
	 */
	public $lastHandledMeetingTime;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'zoomVendorIntegrationId',
		'lastHandledMeetingTime'
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($sourceObject, $responseProfile);
		
		/* @var ZoomVendorIntegration $vendorIntegration */
		$vendorIntegration = VendorIntegrationPeer::retrieveByPK($this->zoomVendorIntegrationId);
		try
		{
			if ($vendorIntegration)
			{
				$headerData = self::getZoomHeaderData();
				$this->baseURL = $headerData[2];
				$this->accountId = $vendorIntegration->getAccountId();
				$this->refreshToken = $vendorIntegration->getRefreshToken();
				$this->accessToken = $vendorIntegration->getAccessToken();
				$this->description = $vendorIntegration->getZoomAccountDescription();
				$this->accessExpiresIn = $vendorIntegration->getExpiresIn();
				$this->zoomAuthType = $vendorIntegration->getZoomAuthType();

				if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID &&
					$vendorIntegration->getExpiresIn() <= time() +
					kconf::getArrayValue('tokenExpiryGrace', 'ZoomAccount', 'vendor', 600))
				{
					KalturaLog::debug('Token expired for account id: ' . $vendorIntegration->getAccountId() . ' renewing with the new tokens');
					$freshTokens = kZoomOauth::refreshTokens($vendorIntegration);
					if ($freshTokens)
					{
						$this->accessToken = $freshTokens[kZoomTokens::ACCESS_TOKEN];
						$this->refreshToken = isset($freshTokens[kZoomTokens::REFRESH_TOKEN]) ? $freshTokens[kZoomTokens::REFRESH_TOKEN] : null;
						$this->accessExpiresIn = kZoomOauth::getTokenExpiryRelativeTime($freshTokens[kZoomTokens::EXPIRES_IN]);
					}
				}
				
				
				$zoomIntegrationObject = new KalturaZoomIntegrationSetting();
				$zoomIntegrationObject->fromObject($vendorIntegration);
				$this->zoomVendorIntegration = $zoomIntegrationObject;
			}
			else
			{
				throw new KalturaAPIException(KalturaZoomDropFolderErrors::DROP_FOLDER_INTEGRATION_DATA_MISSING);
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
			/* @var ZoomVendorIntegration $vendorIntegration */
			$vendorIntegration = VendorIntegrationPeer::retrieveByPK($dbObject->getZoomVendorIntegrationId());
			if (!$vendorIntegration)
			{
				throw new KalturaAPIException(KalturaZoomDropFolderErrors::DROP_FOLDER_INTEGRATION_DATA_MISSING);
			}
			$vendorIntegration->setZoomAccountDescription($this->description);
			$vendorIntegration->save();
		}
		
		if (!$dbObject)
		{
			$dbObject = new ZoomDropFolder();
		}
		
		$dbObject->setType(ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM));
		return parent::toObject($dbObject, $skip);
	}
	
	protected static function getZoomHeaderData()
	{
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		return array($clientId, $clientSecret, $zoomBaseURL);
	}
}