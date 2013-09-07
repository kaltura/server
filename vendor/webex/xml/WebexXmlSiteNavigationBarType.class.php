<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteNavigationBarType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $order;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enabled;
	
	/**
	 *
	 * @var string
	 */
	protected $serviceName;
	
	/**
	 *
	 * @var string
	 */
	protected $customizedName;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'order':
				return 'integer';
	
			case 'enabled':
				return 'boolean';
	
			case 'serviceName':
				return 'string';
	
			case 'customizedName':
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
			'order',
			'enabled',
			'serviceName',
			'customizedName',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'order',
			'serviceName',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'navigationBarType';
	}
	
	/**
	 * @param integer $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}
	
	/**
	 * @return integer $order
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @param boolean $enabled
	 */
	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;
	}
	
	/**
	 * @return boolean $enabled
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}
	
	/**
	 * @param string $serviceName
	 */
	public function setServiceName($serviceName)
	{
		$this->serviceName = $serviceName;
	}
	
	/**
	 * @return string $serviceName
	 */
	public function getServiceName()
	{
		return $this->serviceName;
	}
	
	/**
	 * @param string $customizedName
	 */
	public function setCustomizedName($customizedName)
	{
		$this->customizedName = $customizedName;
	}
	
	/**
	 * @return string $customizedName
	 */
	public function getCustomizedName()
	{
		return $this->customizedName;
	}
	
}
		
