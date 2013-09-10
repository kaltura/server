<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesProductInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
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
				return 'integer';
	
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
		return 'productInstanceType';
	}
	
	/**
	 * @param integer $prodID
	 */
	public function setProdID($prodID)
	{
		$this->prodID = $prodID;
	}
	
	/**
	 * @return integer $prodID
	 */
	public function getProdID()
	{
		return $this->prodID;
	}
	
}
		
