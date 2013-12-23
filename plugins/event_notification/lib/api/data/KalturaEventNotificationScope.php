<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationScope extends KalturaScope
{
	/**
	 * @var string
	 */
	public $scopeObjectId;

	/**
	 * @var KalturaEventNotificationEventObjectType
	 */
	public $scopeObjectType;

	private static $map_between_objects = array
	(
		'scopeObjectId' => 'objectId',
		'scopeObjectType' => 'objectType',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		if (is_null($objectToFill))
			$objectToFill = new kEventNotificationScope();

		/** @var kEventNotificationScope $objectToFill */
		$objectToFill = parent::toObject($objectToFill);

		$objectClassName = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $this->scopeObjectType);
		$peerClass = $objectClassName.'Peer';
		$objectId = $this->scopeObjectId;
		if (class_exists($peerClass))
		{
			$objectToFill->setObject($peerClass::retrieveByPk($objectId));
		}
		else
		{
			$b = new $objectClassName();
			$peer = $b->getPeer();
			$object = $peer::retrieveByPK($objectId);
			$objectToFill->setObject($object);
		}

		if (is_null($objectToFill->getObject()))
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->scopeObjectId);

		return $objectToFill;
	}
}
