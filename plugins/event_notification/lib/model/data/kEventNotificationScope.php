<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventNotificationScope extends kScope implements IKalturaObjectRelatedEvent
{
	/**
	 * @var string
	 */
	protected $objectId;

	/**
	 * @var int
	 */
	protected $objectType;

	/**
	 * @var BaseObject
	 */
	protected $object;

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
		return $this->object;
	}

	/**
	 * @param string $objectId
	 */
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;
	}

	/**
	 * @return string
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	 * @param int $objectType
	 */
	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;
	}

	/**
	 * @return int
	 */
	public function getObjectType()
	{
		return $this->objectType;
	}
}