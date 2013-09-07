<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlAuthenticateUser extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $sessionTicket;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionTicket':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $sessionTicket
	 */
	public function getSessionTicket()
	{
		return $this->sessionTicket;
	}
	
}
		
