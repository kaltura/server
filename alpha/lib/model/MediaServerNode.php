<?php

abstract class MediaServerNode extends DeliveryServerNode {	
	
	protected $partner_media_server_config = null;
	
	const CUSTOM_DATA_PROTOCOL_PORT_CONFIG_ARRAY = 'media_server_port_config';
	const CUSTOM_DATA_PLAYBACK_DOMAIN_CONFIG_ARRAY = 'media_server_playback_domain_config';
	const CUSTOM_DATA_APPLICATION_NAME = 'application_name';
	const CUSTOM_DATA_APP_PREFIX = 'app_prefix';
	const DEFAULT_APPLICATION = 'kLive';
	const ENTRY_ID_URL_PARAM = 'e';
	const PARTNER_ID_URL_PARAM = 'p';
	const EXPLICIT_LIVE_VIEWER_TYPE_URL = 'type';
	const USER_TYPE_ADMIN = 'admin';
	const USER_TYPE_USER = 'user';
	
	abstract public function getWebService($serviceName);
	abstract public function getLiveWebServiceName();
	abstract public function getEnvDc();

	public function getAppNameAndPrefix()
	{
		return '';
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/Baseservernode#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		if($this->getPartnerId() === Partner::MEDIA_SERVER_PARTNER_ID)
			$this->setDc(kDataCenterMgr::getCurrentDcId());
		
		return parent::preInsert($con);
	}
	
	public function getIsExternalMediaServer()
	{
		return $this->getPartnerId() !== Partner::MEDIA_SERVER_PARTNER_ID;
	}
	
	public function setMediaServerPortConfig($mediaServerPortConfig)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL_PORT_CONFIG_ARRAY, $mediaServerPortConfig);
	}
	
	public function getMediaServerPortConfig()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL_PORT_CONFIG_ARRAY, null, null);
	}
	
	public function setMediaServerPlaybackDomainConfig($mediaServerPlaybackDomainConfig)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAYBACK_DOMAIN_CONFIG_ARRAY, $mediaServerPlaybackDomainConfig);
	}
	
	public function getMediaServerPlaybackDomainConfig()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PLAYBACK_DOMAIN_CONFIG_ARRAY, null, null);
	}
	
	public function setApplicationName($applicationName)
	{
		$this->putInCustomData(self::CUSTOM_DATA_APPLICATION_NAME, $applicationName);
	}
	
	public function getApplicationName()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_APPLICATION_NAME, null, self::DEFAULT_APPLICATION);
	}
	
	public function setPartnerMediaServerConfig($partnerMediaServerConfiguration)
	{
		$this->partner_media_server_config = $partnerMediaServerConfiguration;
	}

	public function setAppPrefix($appPrefix)
	{
		$this->putInCustomData(self::CUSTOM_DATA_APP_PREFIX, $appPrefix);
	}

	public function getAppPrefix()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_APP_PREFIX, null, null);
	}

    public static function getSegmentDurationUrlString($sd)
    {
        return '';
    }

	public static function getSessionType($entryServerNode)
	{
		return '';
	}

	public function createThumbUrl($baseUrl, $entry, $entryServerNode)
	{
		return 'Not Implemented';
	}

	public function getPartnerIdUrl(DeliveryProfileDynamicAttributes $da)
	{
		$partnerId = $da->getEntry()->getPartnerId();
		return '/' . self::PARTNER_ID_URL_PARAM . '/' . $partnerId;
	}

	public function getEntryIdUrl(DeliveryProfileDynamicAttributes $da)
	{
		$entryId = $da->getEntryId();
		return '/' . self::ENTRY_ID_URL_PARAM . "/$entryId/";
	}

	public static function modifyUrlForVodFromLive($liveUrl, DeliveryProfileDynamicAttributes $da)
	{
		return $liveUrl;
	}

	protected function getUserType($isAdmin)
	{
		return $isAdmin ? self::USER_TYPE_ADMIN : self::USER_TYPE_USER;
	}

	protected function getUrlType()
	{
		return self::EXPLICIT_LIVE_VIEWER_TYPE_URL;
	}

	public function getExplicitLiveUrl($liveUrl, LiveStreamEntry $entry)
	{
		$userType = $this->getUserType(true);
		if ($entry->getExplicitLive() && !$entry->canViewExplicitLive())
		{
			$userType = $this->getUserType(false);
		}
		return $this->getUrlType() . "/$userType/";
	}

    /**
     * @return string
     */
    public function getAdditionalUrlParam(LiveStreamEntry $entry, LiveEntryServerNode $liveEntryServerNode)
    {
        return '';
    }

} // MediaServerNode
