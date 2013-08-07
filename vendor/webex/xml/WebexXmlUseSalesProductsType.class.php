<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSalesProductsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<integer>
	 */
	protected $prodID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'prodID':
				return 'WebexXmlArray<integer>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'prodID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'prodID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'salesProductsType';
	}
	
	/**
	 * @param WebexXmlArray<integer> $prodID
	 */
	public function setProdID($prodID)
	{
		if($prodID->getType() != 'integer')
			throw new WebexXmlException(get_class($this) . "::prodID must be of type integer");
		
		$this->prodID = $prodID;
	}
	
	/**
	 * @return WebexXmlArray $prodID
	 */
	public function getProdID()
	{
		return $this->prodID;
	}
	
}
		
