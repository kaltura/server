<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSetUser extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlUseSalesCenterType
	 */
	protected $salesCenter;
	
	/**
	 *
	 * @var string
	 */
	protected $newWebExId;
	
	/**
	 *
	 * @var boolean
	 */
	protected $syncWebOffice;
	
	/**
	 *
	 * @var boolean
	 */
	protected $validateFormat;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'salesCenter':
				return 'WebexXmlUseSalesCenterType';
	
			case 'newWebExId':
				return 'string';
	
			case 'syncWebOffice':
				return 'boolean';
	
			case 'validateFormat':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'salesCenter',
			'newWebExId',
			'syncWebOffice',
			'validateFormat',
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
		return 'setUser';
	}
	
	/**
	 * @param WebexXmlUseSalesCenterType $salesCenter
	 */
	public function setSalesCenter(WebexXmlUseSalesCenterType $salesCenter)
	{
		$this->salesCenter = $salesCenter;
	}
	
	/**
	 * @return WebexXmlUseSalesCenterType $salesCenter
	 */
	public function getSalesCenter()
	{
		return $this->salesCenter;
	}
	
	/**
	 * @param string $newWebExId
	 */
	public function setNewWebExId($newWebExId)
	{
		$this->newWebExId = $newWebExId;
	}
	
	/**
	 * @return string $newWebExId
	 */
	public function getNewWebExId()
	{
		return $this->newWebExId;
	}
	
	/**
	 * @param boolean $syncWebOffice
	 */
	public function setSyncWebOffice($syncWebOffice)
	{
		$this->syncWebOffice = $syncWebOffice;
	}
	
	/**
	 * @return boolean $syncWebOffice
	 */
	public function getSyncWebOffice()
	{
		return $this->syncWebOffice;
	}
	
	/**
	 * @param boolean $validateFormat
	 */
	public function setValidateFormat($validateFormat)
	{
		$this->validateFormat = $validateFormat;
	}
	
	/**
	 * @return boolean $validateFormat
	 */
	public function getValidateFormat()
	{
		return $this->validateFormat;
	}
	
}
		
