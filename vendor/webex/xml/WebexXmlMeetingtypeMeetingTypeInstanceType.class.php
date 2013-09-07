<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingtypeMeetingTypeInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $meetingTypeID;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComServiceTypeType>
	 */
	protected $serviceTypes;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'meetingTypeID':
				return 'integer';
	
			case 'serviceTypes':
				return 'WebexXmlArray<WebexXmlComServiceTypeType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingTypeID',
			'serviceTypes',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'meetingTypeID',
			'serviceTypes',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'meetingTypeInstanceType';
	}
	
	/**
	 * @param integer $meetingTypeID
	 */
	public function setMeetingTypeID($meetingTypeID)
	{
		$this->meetingTypeID = $meetingTypeID;
	}
	
	/**
	 * @return integer $meetingTypeID
	 */
	public function getMeetingTypeID()
	{
		return $this->meetingTypeID;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlComServiceTypeType> $serviceTypes
	 */
	public function setServiceTypes(WebexXmlArray $serviceTypes)
	{
		if($serviceTypes->getType() != 'WebexXmlComServiceTypeType')
			throw new WebexXmlException(get_class($this) . "::serviceTypes must be of type WebexXmlComServiceTypeType");
		
		$this->serviceTypes = $serviceTypes;
	}
	
	/**
	 * @return WebexXmlArray $serviceTypes
	 */
	public function getServiceTypes()
	{
		return $this->serviceTypes;
	}
	
}

