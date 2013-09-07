<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServMatchingRecordsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $total;
	
	/**
	 *
	 * @var integer
	 */
	protected $returned;
	
	/**
	 *
	 * @var integer
	 */
	protected $startFrom;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'total':
				return 'integer';
	
			case 'returned':
				return 'integer';
	
			case 'startFrom':
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
			'total',
			'returned',
			'startFrom',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'total',
			'returned',
			'startFrom',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'matchingRecordsType';
	}
	
	/**
	 * @param integer $total
	 */
	public function setTotal($total)
	{
		$this->total = $total;
	}
	
	/**
	 * @return integer $total
	 */
	public function getTotal()
	{
		return $this->total;
	}
	
	/**
	 * @param integer $returned
	 */
	public function setReturned($returned)
	{
		$this->returned = $returned;
	}
	
	/**
	 * @return integer $returned
	 */
	public function getReturned()
	{
		return $this->returned;
	}
	
	/**
	 * @param integer $startFrom
	 */
	public function setStartFrom($startFrom)
	{
		$this->startFrom = $startFrom;
	}
	
	/**
	 * @return integer $startFrom
	 */
	public function getStartFrom()
	{
		return $this->startFrom;
	}
	
}
		
