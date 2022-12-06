<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.objects
 */
class KalturaWebexAPIDropFolder extends KalturaDropFolder
{
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
	
	
	private static $map_between_objects = array(
		'webexAPIVendorIntegrationId',
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
				$webexConfiguration = WebexAPIDropFolderPlugin::getWebexConfiguration();
				$this->baseURL = $webexConfiguration[WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_BASE_URL];
				$this->clientId = $webexConfiguration[WebexAPIDropFolderPlugin::CONFIGURATION_CLIENT_ID];
				
				$this->refreshToken = $vendorIntegration->getRefreshToken();
				$this->accessToken = $vendorIntegration->getAccessToken();
				$this->accessExpiresIn = $vendorIntegration->getExpiresIn();
				
				$tokenExpiryGrace = kConf::getArrayValue(WebexAPIDropFolderPlugin::CONFIGURATION_TOKEN_EXPIRY_GRACE, WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_ACCOUNT_PARAM, WebexAPIDropFolderPlugin::CONFIGURATION_VENDOR_MAP, 600);
				if ($this->accessToken && $this->refreshToken && kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID
					&& $this->accessExpiresIn < time() + $tokenExpiryGrace)
				{
					KalturaLog::info("Refreshing access token for Webex drop folder [{$this->id}], token expires on: {$this->accessExpiresIn}");
					$tokens = kWebexAPIOauth::requestAccessToken($this->refreshToken);
					if ($tokens)
					{
						$vendorIntegration->saveTokensData($tokens);
						$this->refreshToken = $vendorIntegration->getRefreshToken();
						$this->accessToken = $vendorIntegration->getAccessToken();
						$this->accessExpiresIn = $vendorIntegration->getExpiresIn();
					}
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
			$vendorIntegration->setWebexAccountDescription($this->description);
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
