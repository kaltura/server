<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTspServerType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $enableAdaptor;
	
	/**
	 *
	 * @var string
	 */
	protected $serverIP;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteMpAudioType>
	 */
	protected $mpAudio;
	
	/**
	 *
	 * @var string
	 */
	protected $globalCallInNumURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enableAdaptor':
				return 'boolean';
	
			case 'serverIP':
				return 'string';
	
			case 'mpAudio':
				return 'WebexXmlArray<WebexXmlSiteMpAudioType>';
	
			case 'globalCallInNumURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'enableAdaptor',
			'serverIP',
			'mpAudio',
			'globalCallInNumURL',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'enableAdaptor',
			'serverIP',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'tspServerType';
	}
	
	/**
	 * @param boolean $enableAdaptor
	 */
	public function setEnableAdaptor($enableAdaptor)
	{
		$this->enableAdaptor = $enableAdaptor;
	}
	
	/**
	 * @return boolean $enableAdaptor
	 */
	public function getEnableAdaptor()
	{
		return $this->enableAdaptor;
	}
	
	/**
	 * @param string $serverIP
	 */
	public function setServerIP($serverIP)
	{
		$this->serverIP = $serverIP;
	}
	
	/**
	 * @return string $serverIP
	 */
	public function getServerIP()
	{
		return $this->serverIP;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSiteMpAudioType> $mpAudio
	 */
	public function setMpAudio(WebexXmlArray $mpAudio)
	{
		if($mpAudio->getType() != 'WebexXmlSiteMpAudioType')
			throw new WebexXmlException(get_class($this) . "::mpAudio must be of type WebexXmlSiteMpAudioType");
		
		$this->mpAudio = $mpAudio;
	}
	
	/**
	 * @return WebexXmlArray $mpAudio
	 */
	public function getMpAudio()
	{
		return $this->mpAudio;
	}
	
	/**
	 * @param string $globalCallInNumURL
	 */
	public function setGlobalCallInNumURL($globalCallInNumURL)
	{
		$this->globalCallInNumURL = $globalCallInNumURL;
	}
	
	/**
	 * @return string $globalCallInNumURL
	 */
	public function getGlobalCallInNumURL()
	{
		return $this->globalCallInNumURL;
	}
	
}
		
