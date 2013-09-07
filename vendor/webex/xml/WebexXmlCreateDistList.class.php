<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlCreateDistList extends WebexXmlObject
{
	/**
	 *
	 * @var integer
	 */
	protected $distListID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'distListID':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return integer $distListID
	 */
	public function getDistListID()
	{
		return $this->distListID;
	}
	
}

