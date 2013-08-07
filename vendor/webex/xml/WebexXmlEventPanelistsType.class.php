<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventPanelistsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $panelistPassword;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComPersonType>
	 */
	protected $panelist;
	
	/**
	 *
	 * @var string
	 */
	protected $panelistsInfo;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'panelistPassword':
				return 'string';
	
			case 'panelist':
				return 'WebexXmlArray<WebexXmlComPersonType>';
	
			case 'panelistsInfo':
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
			'panelistPassword',
			'panelist',
			'panelistsInfo',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'panelistsType';
	}
	
	/**
	 * @param string $panelistPassword
	 */
	public function setPanelistPassword($panelistPassword)
	{
		$this->panelistPassword = $panelistPassword;
	}
	
	/**
	 * @return string $panelistPassword
	 */
	public function getPanelistPassword()
	{
		return $this->panelistPassword;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlComPersonType> $panelist
	 */
	public function setPanelist(WebexXmlArray $panelist)
	{
		if($panelist->getType() != 'WebexXmlComPersonType')
			throw new WebexXmlException(get_class($this) . "::panelist must be of type WebexXmlComPersonType");
		
		$this->panelist = $panelist;
	}
	
	/**
	 * @return WebexXmlArray $panelist
	 */
	public function getPanelist()
	{
		return $this->panelist;
	}
	
	/**
	 * @param string $panelistsInfo
	 */
	public function setPanelistsInfo($panelistsInfo)
	{
		$this->panelistsInfo = $panelistsInfo;
	}
	
	/**
	 * @return string $panelistsInfo
	 */
	public function getPanelistsInfo()
	{
		return $this->panelistsInfo;
	}
	
}
		
