<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseUserInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $registrationDate;
	
	/**
	 *
	 * @var integer
	 */
	protected $visitCount;
	
	/**
	 *
	 * @var long
	 */
	protected $userId;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'registrationDate':
				return 'string';
	
			case 'visitCount':
				return 'integer';
	
			case 'userId':
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
			'registrationDate',
			'visitCount',
			'userId',
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
		return 'userInstanceType';
	}
	
	/**
	 * @param string $registrationDate
	 */
	public function setRegistrationDate($registrationDate)
	{
		$this->registrationDate = $registrationDate;
	}
	
	/**
	 * @return string $registrationDate
	 */
	public function getRegistrationDate()
	{
		return $this->registrationDate;
	}
	
	/**
	 * @param integer $visitCount
	 */
	public function setVisitCount($visitCount)
	{
		$this->visitCount = $visitCount;
	}
	
	/**
	 * @return integer $visitCount
	 */
	public function getVisitCount()
	{
		return $this->visitCount;
	}
	
	/**
	 * @param long $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	/**
	 * @return long $userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}
	
}
		
