<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpListControlType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $startFrom;
	
	/**
	 *
	 * @var long
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
				return 'long';
	
			case 'maximumNum':
				return 'long';
	
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
			'startFrom',
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
	 * @param long $startFrom
	 */
	public function setStartFrom($startFrom)
	{
		$this->startFrom = $startFrom;
	}
	
	/**
	 * @return long $startFrom
	 */
	public function getStartFrom()
	{
		return $this->startFrom;
	}
	
	/**
	 * @param long $maximumNum
	 */
	public function setMaximumNum($maximumNum)
	{
		$this->maximumNum = $maximumNum;
	}
	
	/**
	 * @return long $maximumNum
	 */
	public function getMaximumNum()
	{
		return $this->maximumNum;
	}
	
}
		
