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
				$this->refreshToken = $vendorIntegration->getRefreshToken();
				$this->accessToken = $vendorIntegration->getAccessToken();
				$this->accessExpiresIn = $vendorIntegration->getExpiresIn();
				
				if ($this->accessToken && $this->refreshToken && kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID
					&& $vendorIntegration->getExpiresIn() < time() + kTimeConversion::MINUTE * 5)
				{
					$webexConfiguration = WebexAPIDropFolderPlugin::getWebexConfiguration();
					$this->clientId = $webexConfiguration['clientId'];
					$this->baseURL = $webexConfiguration['baseUrl'];
					$tokens = kWebexAPIOauth::requestAccessToken($this->refreshToken);
					$vendorIntegration->saveTokensData($tokens);
					$this->refreshToken = $vendorIntegration->getRefreshToken();
					$this->accessToken = $vendorIntegration->getAccessToken();
					$this->accessExpiresIn = $vendorIntegration->getExpiresIn();
				}
				
				$webexAPIIntegrationObject = new KalturaWebexAPIIntegrationSetting();
				$webexAPIIntegrationObject->fromObject($vendorIntegration);
				$this->webexAPIVendorIntegration = $webexAPIIntegrationObject;
			}
			else
			{
				throw new KalturaAPIException(KalturaWebexAPIErrors::DROP_FOLDER_INTEGRATION_DATA_MISSING);
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
				throw new KalturaAPIException(KalturaWebexAPIErrors::DROP_FOLDER_INTEGRATION_DATA_MISSING);
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