<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlCreateTeleconferenceSession extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionKey':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $sessionKey
	 */
	public function getSessionKey()
	{
		return $this->sessionKey;
	}
	
}
		
