<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/string.class.php');

class WebexXmlSendInvitationEmail extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $deliveredEmail;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'deliveredEmail':
				return 'WebexXmlArray<string>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $deliveredEmail
	 */
	public function getDeliveredEmail()
	{
		return $this->deliveredEmail;
	}
	
}

