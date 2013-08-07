<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventSetEvent extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEventEnrollmentType
	 */
	protected $enrollment;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFormSetType
	 */
	protected $enrollmentForm;
	
	/**
	 *
	 * @var boolean
	 */
	protected $validateFormat;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enrollment':
				return 'WebexXmlEventEnrollmentType';
	
			case 'enrollmentForm':
				return 'WebexXmlEventEnrollmentFormSetType';
	
			case 'validateFormat':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'enrollment',
			'enrollmentForm',
			'validateFormat',
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
		return 'setEvent';
	}
	
	/**
	 * @param WebexXmlEventEnrollmentType $enrollment
	 */
	public function setEnrollment(WebexXmlEventEnrollmentType $enrollment)
	{
		$this->enrollment = $enrollment;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentType $enrollment
	 */
	public function getEnrollment()
	{
		return $this->enrollment;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFormSetType $enrollmentForm
	 */
	public function setEnrollmentForm(WebexXmlEventEnrollmentFormSetType $enrollmentForm)
	{
		$this->enrollmentForm = $enrollmentForm;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFormSetType $enrollmentForm
	 */
	public function getEnrollmentForm()
	{
		return $this->enrollmentForm;
	}
	
	/**
	 * @param boolean $validateFormat
	 */
	public function setValidateFormat($validateFormat)
	{
		$this->validateFormat = $validateFormat;
	}
	
	/**
	 * @return boolean $validateFormat
	 */
	public function getValidateFormat()
	{
		return $this->validateFormat;
	}
	
}
		
