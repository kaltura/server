<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGetLoginTicket extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $ticket;
	
	/**
	 *
	 * @var string
	 */
	protected $apiVersion;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'ticket':
				return 'string';
	
			case 'apiVersion':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $ticket
	 */
	public function getTicket()
	{
		return $this->ticket;
	}
	
	/**
	 * @return string $apiVersion
	 */
	public function getApiVersion()
	{
		return $this->apiVersion;
	}
	
}

