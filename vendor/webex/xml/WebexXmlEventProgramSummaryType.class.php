<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventProgramSummaryType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $programID;
	
	/**
	 *
	 * @var string
	 */
	protected $programName;
	
	/**
	 *
	 * @var string
	 */
	protected $hostWebExID;
	
	/**
	 *
	 * @var string
	 */
	protected $expectedEnrollment;
	
	/**
	 *
	 * @var string
	 */
	protected $budget;
	
	/**
	 *
	 * @var WebexXmlEventListingType
	 */
	protected $status;
	
	/**
	 *
	 * @var string
	 */
	protected $programURL;
	
	/**
	 *
	 * @var string
	 */
	protected $afterEnrollmentURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'programID':
				return 'long';
	
			case 'programName':
				return 'string';
	
			case 'hostWebExID':
				return 'string';
	
			case 'expectedEnrollment':
				return 'string';
	
			case 'budget':
				return 'string';
	
			case 'status':
				return 'WebexXmlEventListingType';
	
			case 'programURL':
				return 'string';
	
			case 'afterEnrollmentURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'programID',
			'programName',
			'hostWebExID',
			'expectedEnrollment',
			'budget',
			'status',
			'programURL',
			'afterEnrollmentURL',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'programID',
			'programName',
			'hostWebExID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'programSummaryType';
	}
	
	/**
	 * @param long $programID
	 */
	public function setProgramID($programID)
	{
		$this->programID = $programID;
	}
	
	/**
	 * @return long $programID
	 */
	public function getProgramID()
	{
		return $this->programID;
	}
	
	/**
	 * @param string $programName
	 */
	public function setProgramName($programName)
	{
		$this->programName = $programName;
	}
	
	/**
	 * @return string $programName
	 */
	public function getProgramName()
	{
		return $this->programName;
	}
	
	/**
	 * @param string $hostWebExID
	 */
	public function setHostWebExID($hostWebExID)
	{
		$this->hostWebExID = $hostWebExID;
	}
	
	/**
	 * @return string $hostWebExID
	 */
	public function getHostWebExID()
	{
		return $this->hostWebExID;
	}
	
	/**
	 * @param string $expectedEnrollment
	 */
	public function setExpectedEnrollment($expectedEnrollment)
	{
		$this->expectedEnrollment = $expectedEnrollment;
	}
	
	/**
	 * @return string $expectedEnrollment
	 */
	public function getExpectedEnrollment()
	{
		return $this->expectedEnrollment;
	}
	
	/**
	 * @param string $budget
	 */
	public function setBudget($budget)
	{
		$this->budget = $budget;
	}
	
	/**
	 * @return string $budget
	 */
	public function getBudget()
	{
		return $this->budget;
	}
	
	/**
	 * @param WebexXmlEventListingType $status
	 */
	public function setStatus(WebexXmlEventListingType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return WebexXmlEventListingType $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
	 * @param string $programURL
	 */
	public function setProgramURL($programURL)
	{
		$this->programURL = $programURL;
	}
	
	/**
	 * @return string $programURL
	 */
	public function getProgramURL()
	{
		return $this->programURL;
	}
	
	/**
	 * @param string $afterEnrollmentURL
	 */
	public function setAfterEnrollmentURL($afterEnrollmentURL)
	{
		$this->afterEnrollmentURL = $afterEnrollmentURL;
	}
	
	/**
	 * @return string $afterEnrollmentURL
	 */
	public function getAfterEnrollmentURL()
	{
		return $this->afterEnrollmentURL;
	}
	
}
		
