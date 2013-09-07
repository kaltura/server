<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlCreateOpportunity extends WebexXmlObject
{
	/**
	 *
	 * @var integer
	 */
	protected $intOpptyID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'intOpptyID':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return integer $intOpptyID
	 */
	public function getIntOpptyID()
	{
		return $this->intOpptyID;
	}
	
}

