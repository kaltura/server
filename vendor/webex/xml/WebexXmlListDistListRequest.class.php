<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListDistList.class.php');
require_once(__DIR__ . '/WebexXmlComAddressTypeType.class.php');

class WebexXmlListDistListRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var integer
	 */
	protected $distListID;
	
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var WebexXmlComAddressTypeType
	 */
	protected $addressType;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'distListID',
			'name',
			'addressType',
			'hostWebExID',
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
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:lstDistList';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListDistList';
	}
	
	/**
	 * @param integer $distListID
	 */
	public function setDistListID($distListID)
	{
		$this->distListID = $distListID;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @param WebexXmlComAddressTypeType $addressType
	 */
	public function setAddressType(WebexXmlComAddressTypeType $addressType)
	{
		$this->addressType = $addressType;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
}
		
