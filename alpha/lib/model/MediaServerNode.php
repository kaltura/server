<?php

abstract class MediaServerNode extends DeliveryServerNode {	
	
	protected $partner_media_server_config = null;
	
	const CUSTOM_DATA_PROTOCOL_PORT_CONFIG_ARRAY = 'protocol_port_config';
	const CUSTOM_DATA_APPLICATION_NAME = 'application_name';
	const CUSTOM_DATA_IS_EXTERNAL = 'is_external';
	const DEFAULT_APPLICATION = 'kLive';
	
	abstract public function getWebService($serviceName);
	abstract public function getLiveWebServiceName();
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Baseservernode#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		if($this->getPartnerId() !== Partner::MEDIA_SERVER_PARTNER_ID)
			$this->setIsExternalMediaServer(true);
		else
			$this->setDc(kDataCenterMgr::getCurrentDcId());
		
		return parent::preInsert($con);
	}
	
	public function setIsExternalMediaServer($isExternal)
	{
		$this->putInCustomData(self::CUSTOM_DATA_IS_EXTERNAL, $isExternal);
	}
	
	public function getIsExternalMediaServer()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_IS_EXTERNAL, null, false);
	}
	
	public function setProtocolPortConfig($protocolPortConfigArray)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL_PORT_CONFIG_ARRAY, $protocolPortConfigArray);
	}
	
	public function getProtocolPortConfig()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL_PORT_CONFIG_ARRAY, null, array());
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

} // MediaServerNode
