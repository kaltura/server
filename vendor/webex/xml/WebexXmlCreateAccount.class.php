<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlCreateAccount extends WebexXmlObject
{
	/**
	 *
	 * @var integer
	 */
	protected $intAccountID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'intAccountID':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return integer $intAccountID
	 */
	public function getIntAccountID()
	{
		return $this->intAccountID;
	}
	
}

