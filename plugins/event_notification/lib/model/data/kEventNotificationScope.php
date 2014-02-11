<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventNotificationScope extends kEventScope
{
	/**
	 * @var BaseObject
	 */
	protected $object;

	public function __construct()
	{
		parent::__construct(null);
	}

	/**
	 * @param \BaseObject $object
	 */
	public function setObject($object)
	{
		$this->object = $object;
	}

	/**
	 * @return \BaseObject
	 */
	public function getObject()
	{
		if (parent::getObject())
			return parent::getObject();
		else
			return $this->object;
	}
}