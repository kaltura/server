<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/integer.class.php');

class WebexXmlSetProducts extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<integer>
	 */
	protected $prodID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'prodID':
				return 'WebexXmlArray<integer>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $prodID
	 */
	public function getProdID()
	{
		return $this->prodID;
	}
	
}

