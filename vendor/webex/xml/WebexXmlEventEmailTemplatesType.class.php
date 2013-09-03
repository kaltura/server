<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEmailTemplatesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEventFormatType
	 */
	protected $format;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $invitationMsgs;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $enrollmentMsgs;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $reminderMsgs;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $followUpMsgs;
	
	/**
	 *
	 * @var boolean
	 */
	protected $iCalendar;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'format':
				return 'WebexXmlEventFormatType';
	
			case 'invitationMsgs':
				return 'WebexXml';
	
			case 'enrollmentMsgs':
				return 'WebexXml';
	
			case 'reminderMsgs':
				return 'WebexXml';
	
			case 'followUpMsgs':
				return 'WebexXml';
	
			case 'iCalendar':
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
			'format',
			'invitationMsgs',
			'enrollmentMsgs',
			'reminderMsgs',
			'followUpMsgs',
			'iCalendar',
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
		return 'emailTemplatesType';
	}
	
	/**
	 * @param WebexXmlEventFormatType $format
	 */
	public function setFormat(WebexXmlEventFormatType $format)
	{
		$this->format = $format;
	}
	
	/**
	 * @return WebexXmlEventFormatType $format
	 */
	public function getFormat()
	{
		return $this->format;
	}
	
	/**
	 * @param WebexXml $invitationMsgs
	 */
	public function setInvitationMsgs(WebexXml $invitationMsgs)
	{
		$this->invitationMsgs = $invitationMsgs;
	}
	
	/**
	 * @return WebexXml $invitationMsgs
	 */
	public function getInvitationMsgs()
	{
		return $this->invitationMsgs;
	}
	
	/**
	 * @param WebexXml $enrollmentMsgs
	 */
	public function setEnrollmentMsgs(WebexXml $enrollmentMsgs)
	{
		$this->enrollmentMsgs = $enrollmentMsgs;
	}
	
	/**
	 * @return WebexXml $enrollmentMsgs
	 */
	public function getEnrollmentMsgs()
	{
		return $this->enrollmentMsgs;
	}
	
	/**
	 * @param WebexXml $reminderMsgs
	 */
	public function setReminderMsgs(WebexXml $reminderMsgs)
	{
		$this->reminderMsgs = $reminderMsgs;
	}
	
	/**
	 * @return WebexXml $reminderMsgs
	 */
	public function getReminderMsgs()
	{
		return $this->reminderMsgs;
	}
	
	/**
	 * @param WebexXml $followUpMsgs
	 */
	public function setFollowUpMsgs(WebexXml $followUpMsgs)
	{
		$this->followUpMsgs = $followUpMsgs;
	}
	
	/**
	 * @return WebexXml $followUpMsgs
	 */
	public function getFollowUpMsgs()
	{
		return $this->followUpMsgs;
	}
	
	/**
	 * @param boolean $iCalendar
	 */
	public function setICalendar($iCalendar)
	{
		$this->iCalendar = $iCalendar;
	}
	
	/**
	 * @return boolean $iCalendar
	 */
	public function getICalendar()
	{
		return $this->iCalendar;
	}
	
}
		
