<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeAttendeeStatusType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $invited;
	
	/**
	 *
	 * @var boolean
	 */
	protected $registered;
	
	/**
	 *
	 * @var boolean
	 */
	protected $rejected;
	
	/**
	 *
	 * @var boolean
	 */
	protected $accepted;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'invited':
				return 'boolean';
	
			case 'registered':
				return 'boolean';
	
			case 'rejected':
				return 'boolean';
	
			case 'accepted':
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
			'invited',
			'registered',
			'rejected',
			'accepted',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'invited',
			'registered',
			'rejected',
			'accepted',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'attendeeStatusType';
	}
	
	/**
	 * @param boolean $invited
	 */
	public function setInvited($invited)
	{
		$this->invited = $invited;
	}
	
	/**
	 * @return boolean $invited
	 */
	public function getInvited()
	{
		return $this->invited;
	}
	
	/**
	 * @param boolean $registered
	 */
	public function setRegistered($registered)
	{
		$this->registered = $registered;
	}
	
	/**
	 * @return boolean $registered
	 */
	public function getRegistered()
	{
		return $this->registered;
	}
	
	/**
	 * @param boolean $rejected
	 */
	public function setRejected($rejected)
	{
		$this->rejected = $rejected;
	}
	
	/**
	 * @return boolean $rejected
	 */
	public function getRejected()
	{
		return $this->rejected;
	}
	
	/**
	 * @param boolean $accepted
	 */
	public function setAccepted($accepted)
	{
		$this->accepted = $accepted;
	}
	
	/**
	 * @return boolean $accepted
	 */
	public function getAccepted()
	{
		return $this->accepted;
	}
	
}
		
