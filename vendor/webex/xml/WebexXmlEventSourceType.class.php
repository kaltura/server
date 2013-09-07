<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventSourceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $leadSourceID;
	
	/**
	 *
	 * @var long
	 */
	protected $count;
	
	/**
	 *
	 * @var float
	 */
	protected $avgLeadScore;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'leadSourceID':
				return 'string';
	
			case 'count':
				return 'long';
	
			case 'avgLeadScore':
				return 'float';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'leadSourceID',
			'count',
			'avgLeadScore',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'leadSourceID',
			'count',
			'avgLeadScore',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'sourceType';
	}
	
	/**
	 * @param string $leadSourceID
	 */
	public function setLeadSourceID($leadSourceID)
	{
		$this->leadSourceID = $leadSourceID;
	}
	
	/**
	 * @return string $leadSourceID
	 */
	public function getLeadSourceID()
	{
		return $this->leadSourceID;
	}
	
	/**
	 * @param long $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}
	
	/**
	 * @return long $count
	 */
	public function getCount()
	{
		return $this->count;
	}
	
	/**
	 * @param float $avgLeadScore
	 */
	public function setAvgLeadScore($avgLeadScore)
	{
		$this->avgLeadScore = $avgLeadScore;
	}
	
	/**
	 * @return float $avgLeadScore
	 */
	public function getAvgLeadScore()
	{
		return $this->avgLeadScore;
	}
	
}
		
