<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServListControlType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $startFrom;
	
	/**
	 *
	 * @var integer
	 */
	protected $maximumNum;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'startFrom':
				return 'integer';
	
			case 'maximumNum':
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
			'startFrom',
			'maximumNum',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'listControlType';
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
	
	/**
	 * @param integer $maximumNum
	 */
	public function setMaximumNum($maximumNum)
	{
		$this->maximumNum = $maximumNum;
	}
	
	/**
	 * @return integer $maximumNum
	 */
	public function getMaximumNum()
	{
		return $this->maximumNum;
	}
	
}
		
