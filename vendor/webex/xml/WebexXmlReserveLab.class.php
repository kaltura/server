<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlReserveLab extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $holSessionID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'holSessionID':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $holSessionID
	 */
	public function getHolSessionID()
	{
		return $this->holSessionID;
	}
	
}
		
