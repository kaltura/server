<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseDelUserStatusType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $webExId;
	
	/**
	 *
	 * @var WebexXmlServResultTypeType
	 */
	protected $status;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'webExId':
				return 'string';
	
			case 'status':
				return 'WebexXmlServResultTypeType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExId',
			'status',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'webExId',
			'status',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'delUserStatusType';
	}
	
	/**
	 * @param string $webExId
	 */
	public function setWebExId($webExId)
	{
		$this->webExId = $webExId;
	}
	
	/**
	 * @return string $webExId
	 */
	public function getWebExId()
	{
		return $this->webExId;
	}
	
	/**
	 * @param WebexXmlServResultTypeType $status
	 */
	public function setStatus(WebexXmlServResultTypeType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return WebexXmlServResultTypeType $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
}
		
