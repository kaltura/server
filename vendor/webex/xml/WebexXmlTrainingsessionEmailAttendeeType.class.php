<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionEmailAttendeeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $invite;
	
	/**
	 *
	 * @var boolean
	 */
	protected $reminderAfterStart;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $beforeDays;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $beforeHours;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $beforeMinutes;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendToRegister;
	
	/**
	 *
	 * @var boolean
	 */
	protected $notifySubmits;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'invite':
				return 'boolean';
	
			case 'reminderAfterStart':
				return 'boolean';
	
			case 'beforeDays':
				return 'WebexXml';
	
			case 'beforeHours':
				return 'WebexXml';
	
			case 'beforeMinutes':
				return 'WebexXml';
	
			case 'sendToRegister':
				return 'boolean';
	
			case 'notifySubmits':
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
			'invite',
			'reminderAfterStart',
			'beforeDays',
			'beforeHours',
			'beforeMinutes',
			'sendToRegister',
			'notifySubmits',
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
		return 'emailAttendeeType';
	}
	
	/**
	 * @param boolean $invite
	 */
	public function setInvite($invite)
	{
		$this->invite = $invite;
	}
	
	/**
	 * @return boolean $invite
	 */
	public function getInvite()
	{
		return $this->invite;
	}
	
	/**
	 * @param boolean $reminderAfterStart
	 */
	public function setReminderAfterStart($reminderAfterStart)
	{
		$this->reminderAfterStart = $reminderAfterStart;
	}
	
	/**
	 * @return boolean $reminderAfterStart
	 */
	public function getReminderAfterStart()
	{
		return $this->reminderAfterStart;
	}
	
	/**
	 * @param WebexXml $beforeDays
	 */
	public function setBeforeDays(WebexXml $beforeDays)
	{
		$this->beforeDays = $beforeDays;
	}
	
	/**
	 * @return WebexXml $beforeDays
	 */
	public function getBeforeDays()
	{
		return $this->beforeDays;
	}
	
	/**
	 * @param WebexXml $beforeHours
	 */
	public function setBeforeHours(WebexXml $beforeHours)
	{
		$this->beforeHours = $beforeHours;
	}
	
	/**
	 * @return WebexXml $beforeHours
	 */
	public function getBeforeHours()
	{
		return $this->beforeHours;
	}
	
	/**
	 * @param WebexXml $beforeMinutes
	 */
	public function setBeforeMinutes(WebexXml $beforeMinutes)
	{
		$this->beforeMinutes = $beforeMinutes;
	}
	
	/**
	 * @return WebexXml $beforeMinutes
	 */
	public function getBeforeMinutes()
	{
		return $this->beforeMinutes;
	}
	
	/**
	 * @param boolean $sendToRegister
	 */
	public function setSendToRegister($sendToRegister)
	{
		$this->sendToRegister = $sendToRegister;
	}
	
	/**
	 * @return boolean $sendToRegister
	 */
	public function getSendToRegister()
	{
		return $this->sendToRegister;
	}
	
	/**
	 * @param boolean $notifySubmits
	 */
	public function setNotifySubmits($notifySubmits)
	{
		$this->notifySubmits = $notifySubmits;
	}
	
	/**
	 * @return boolean $notifySubmits
	 */
	public function getNotifySubmits()
	{
		return $this->notifySubmits;
	}
	
}

