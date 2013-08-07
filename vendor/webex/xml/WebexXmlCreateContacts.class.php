<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/integer.class.php');

class WebexXmlCreateContacts extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<integer>
	 */
	protected $contactID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'contactID':
				return 'WebexXmlArray<integer>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $contactID
	 */
	public function getContactID()
	{
		return $this->contactID;
	}
	
}

