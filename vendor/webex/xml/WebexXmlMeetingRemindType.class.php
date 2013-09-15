<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingRemindType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $enableReminder;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlComEmailType>
	 */
	protected $emails;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendEmail;
	
	/**
	 *
	 * @var string
	 */
	protected $mobile;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendMobile;
	
	/**
	 *
	 * @var integer
	 */
	protected $daysAhead;
	
	/**
	 *
	 * @var integer
	 */
	protected $hoursAhead;
	
	/**
	 *
	 * @var integer
	 */
	protected $minutesAhead;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enableReminder':
				return 'boolean';
	
			case 'emails':
				return 'WebexXmlArray<WebexXmlComEmailType>';
	
			case 'sendEmail':
				return 'boolean';
	
			case 'mobile':
				return 'string';
	
			case 'sendMobile':
				return 'boolean';
	
			case 'daysAhead':
				return 'integer';
	
			case 'hoursAhead':
				return 'integer';
	
			case 'minutesAhead':
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
			'enableReminder',
			'emails',
			'sendEmail',
			'mobile',
			'sendMobile',
			'daysAhead',
			'hoursAhead',
			'minutesAhead',
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
		return 'remindType';
	}
	
	/**
	 * @param boolean $enableReminder
	 */
	public function setEnableReminder($enableReminder)
	{
		$this->enableReminder = $enableReminder;
	}
	
	/**
	 * @return boolean $enableReminder
	 */
	public function getEnableReminder()
	{
		return $this->enableReminder;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlComEmailType> $emails
	 */
	public function setEmails(WebexXmlArray $emails)
	{
		if($emails->getType() != 'WebexXmlComEmailType')
			throw new WebexXmlException(get_class($this) . "::emails must be of type WebexXmlComEmailType");
		
		$this->emails = $emails;
	}
	
	/**
	 * @return WebexXmlArray $emails
	 */
	public function getEmails()
	{
		return $this->emails;
	}
	
	/**
	 * @param boolean $sendEmail
	 */
	public function setSendEmail($sendEmail)
	{
		$this->sendEmail = $sendEmail;
	}
	
	/**
	 * @return boolean $sendEmail
	 */
	public function getSendEmail()
	{
		return $this->sendEmail;
	}
	
	/**
	 * @param string $mobile
	 */
	public function setMobile($mobile)
	{
		$this->mobile = $mobile;
	}
	
	/**
	 * @return string $mobile
	 */
	public function getMobile()
	{
		return $this->mobile;
	}
	
	/**
	 * @param boolean $sendMobile
	 */
	public function setSendMobile($sendMobile)
	{
		$this->sendMobile = $sendMobile;
	}
	
	/**
	 * @return boolean $sendMobile
	 */
	public function getSendMobile()
	{
		return $this->sendMobile;
	}
	
	/**
	 * @param integer $daysAhead
	 */
	public function setDaysAhead($daysAhead)
	{
		$this->daysAhead = $daysAhead;
	}
	
	/**
	 * @return integer $daysAhead
	 */
	public function getDaysAhead()
	{
		return $this->daysAhead;
	}
	
	/**
	 * @param integer $hoursAhead
	 */
	public function setHoursAhead($hoursAhead)
	{
		$this->hoursAhead = $hoursAhead;
	}
	
	/**
	 * @return integer $hoursAhead
	 */
	public function getHoursAhead()
	{
		return $this->hoursAhead;
	}
	
	/**
	 * @param integer $minutesAhead
	 */
	public function setMinutesAhead($minutesAhead)
	{
		$this->minutesAhead = $minutesAhead;
	}
	
	/**
	 * @return integer $minutesAhead
	 */
	public function getMinutesAhead()
	{
		return $this->minutesAhead;
	}
	
}
		
