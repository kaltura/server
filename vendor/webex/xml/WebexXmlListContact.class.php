<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlEpContactType.class.php');

class WebexXmlListContact extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEpContactType>
	 */
	protected $contact;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'contact':
				return 'WebexXmlArray<WebexXmlEpContactType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $contact
	 */
	public function getContact()
	{
		return $this->contact;
	}
	
}

