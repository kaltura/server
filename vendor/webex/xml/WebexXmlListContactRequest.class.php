<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListContact.class.php');
require_once(__DIR__ . '/WebexXmlComAddressTypeType.class.php');

class WebexXmlListContactRequest extends WebexXmlRequestBodyContent
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
	protected $distListName;
	
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
			'distListName',
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
		return 'ep:lstContact';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListContact';
	}
	
	/**
	 * @param integer $distListID
	 */
	public function setDistListID($distListID)
	{
		$this->distListID = $distListID;
	}
	
	/**
	 * @param string $distListName
	 */
	public function setDistListName($distListName)
	{
		$this->distListName = $distListName;
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
		
