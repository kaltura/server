<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlDelUser.class.php');
require_once(__DIR__ . '/string.class.php');

class WebexXmlDelUserRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $webExId;
	
	/**
	 *
	 * @var boolean
	 */
	protected $syncWebOffice;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExId',
			'syncWebOffice',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'webExId',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'use';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'use:delUser';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlDelUser';
	}
	
	/**
	 * @param WebexXmlArray<string> $webExId
	 */
	public function setWebExId($webExId)
	{
		if($webExId->getType() != 'string')
			throw new WebexXmlException(get_class($this) . "::webExId must be of type string");
		
		$this->webExId = $webExId;
	}
	
	/**
	 * @param boolean $syncWebOffice
	 */
	public function setSyncWebOffice($syncWebOffice)
	{
		$this->syncWebOffice = $syncWebOffice;
	}
	
}
		
