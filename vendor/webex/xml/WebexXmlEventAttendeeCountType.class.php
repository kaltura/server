<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventAttendeeCountType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $estEnrollment;
	
	/**
	 *
	 * @var long
	 */
	protected $estAttendance;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventSourceType>
	 */
	protected $source;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'estEnrollment':
				return 'long';
	
			case 'estAttendance':
				return 'long';
	
			case 'source':
				return 'WebexXmlArray<WebexXmlEventSourceType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'estEnrollment',
			'estAttendance',
			'source',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'estEnrollment',
			'estAttendance',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'attendeeCountType';
	}
	
	/**
	 * @param long $estEnrollment
	 */
	public function setEstEnrollment($estEnrollment)
	{
		$this->estEnrollment = $estEnrollment;
	}
	
	/**
	 * @return long $estEnrollment
	 */
	public function getEstEnrollment()
	{
		return $this->estEnrollment;
	}
	
	/**
	 * @param long $estAttendance
	 */
	public function setEstAttendance($estAttendance)
	{
		$this->estAttendance = $estAttendance;
	}
	
	/**
	 * @return long $estAttendance
	 */
	public function getEstAttendance()
	{
		return $this->estAttendance;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventSourceType> $source
	 */
	public function setSource(WebexXmlArray $source)
	{
		if($source->getType() != 'WebexXmlEventSourceType')
			throw new WebexXmlException(get_class($this) . "::source must be of type WebexXmlEventSourceType");
		
		$this->source = $source;
	}
	
	/**
	 * @return WebexXmlArray $source
	 */
	public function getSource()
	{
		return $this->source;
	}
	
}

