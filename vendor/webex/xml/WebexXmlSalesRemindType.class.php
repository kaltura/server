<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesRemindType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $enableReminder;
	
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
		
