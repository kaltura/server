<?php

class kSchedulingICalCalendar extends kSchedulingICalComponent
{
	const VERSION = '2.0';
	const PRODID_PREFIX = '-//Kaltura Inc//Kaltura Server ';
	const PRODID_POSTFIX = '//EN';
	/**
	 * @param string $data
	 * @param KalturaScheduleEventType $eventsType
	 */
	public function __construct($data = null, $eventsType = null)
	{
		$this->setKalturaType($eventsType);
		parent::__construct($data);
	}

	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::getType()
	 */
	protected function getType()
	{
		return kSchedulingICal::TYPE_CALENDAR;
	}

	public function begin($newIcalFormat = null)
	{
		$ret = $this->writeField('BEGIN', $this->getType());
		if ($newIcalFormat)
		{
			$ret .= $this->writeField('PRODID', self::PRODID_PREFIX . mySystemUtils::getVersion() . self::PRODID_POSTFIX);
			$ret .= $this->writeField('VERSION', self::VERSION);
		}
		return $ret;
	}
}