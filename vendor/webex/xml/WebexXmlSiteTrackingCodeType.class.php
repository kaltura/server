<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTrackingCodeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $index;
	
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var string
	 */
	protected $inputMode;
	
	/**
	 *
	 * @var WebexXmlSiteCodeDisplayType
	 */
	protected $hostProfile;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteCodeSchedulingType>
	 */
	protected $schedulingPage;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteCodeListType>
	 */
	protected $listValue;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'index':
				return 'integer';
	
			case 'name':
				return 'string';
	
			case 'inputMode':
				return 'string';
	
			case 'hostProfile':
				return 'WebexXmlSiteCodeDisplayType';
	
			case 'schedulingPage':
				return 'WebexXmlArray<WebexXmlSiteCodeSchedulingType>';
	
			case 'listValue':
				return 'WebexXmlArray<WebexXmlSiteCodeListType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'index',
			'name',
			'inputMode',
			'hostProfile',
			'schedulingPage',
			'listValue',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'index',
			'name',
			'inputMode',
			'hostProfile',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'trackingCodeType';
	}
	
	/**
	 * @param integer $index
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	}
	
	/**
	 * @return integer $index
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param string $inputMode
	 */
	public function setInputMode($inputMode)
	{
		$this->inputMode = $inputMode;
	}
	
	/**
	 * @return string $inputMode
	 */
	public function getInputMode()
	{
		return $this->inputMode;
	}
	
	/**
	 * @param WebexXmlSiteCodeDisplayType $hostProfile
	 */
	public function setHostProfile(WebexXmlSiteCodeDisplayType $hostProfile)
	{
		$this->hostProfile = $hostProfile;
	}
	
	/**
	 * @return WebexXmlSiteCodeDisplayType $hostProfile
	 */
	public function getHostProfile()
	{
		return $this->hostProfile;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSiteCodeSchedulingType> $schedulingPage
	 */
	public function setSchedulingPage(WebexXmlArray $schedulingPage)
	{
		if($schedulingPage->getType() != 'WebexXmlSiteCodeSchedulingType')
			throw new WebexXmlException(get_class($this) . "::schedulingPage must be of type WebexXmlSiteCodeSchedulingType");
		
		$this->schedulingPage = $schedulingPage;
	}
	
	/**
	 * @return WebexXmlArray $schedulingPage
	 */
	public function getSchedulingPage()
	{
		return $this->schedulingPage;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSiteCodeListType> $listValue
	 */
	public function setListValue(WebexXmlArray $listValue)
	{
		if($listValue->getType() != 'WebexXmlSiteCodeListType')
			throw new WebexXmlException(get_class($this) . "::listValue must be of type WebexXmlSiteCodeListType");
		
		$this->listValue = $listValue;
	}
	
	/**
	 * @return WebexXmlArray $listValue
	 */
	public function getListValue()
	{
		return $this->listValue;
	}
	
}
		
