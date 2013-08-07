<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlCreateContacts.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlEpContactType.class.php');

class WebexXmlCreateContactsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEpContactType>
	 */
	protected $contact;
	
	/**
	 *
	 * @var boolean
	 */
	protected $validateFormat;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'contact',
			'validateFormat',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'contact',
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
		return 'ep:createContacts';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlCreateContacts';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEpContactType> $contact
	 */
	public function setContact(WebexXmlArray $contact)
	{
		if($contact->getType() != 'WebexXmlEpContactType')
			throw new WebexXmlException(get_class($this) . "::contact must be of type WebexXmlEpContactType");
		
		$this->contact = $contact;
	}
	
	/**
	 * @param boolean $validateFormat
	 */
	public function setValidateFormat($validateFormat)
	{
		$this->validateFormat = $validateFormat;
	}
	
}
		
