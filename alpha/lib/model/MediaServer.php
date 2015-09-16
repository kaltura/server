<?php

abstract class MediaServerNode extends DeliveryServerNode {	
	
	const CUSTOM_DATA_PROTOCOL_PORT_CONFIG_ARRAY = 'protocol_port_config';
	const CUSTOM_DATA_IS_EXTERNAL = 'is_external';
	
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
	
	public function setIsExternalMediaServer($isInternal)
	{
		$this->putInCustomData(self::CUSTOM_DATA_IS_EXTERNAL, $isInternal);
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

} // MediaServerNode
