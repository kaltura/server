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
	public $objectId;

	/**
	 * @var KalturaEventNotificationEventObjectType
	 */
	public $scopeObjectType;

	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		if (is_null($objectToFill))
			$objectToFill = new kEventNotificationScope();

		/** @var kEventNotificationScope $objectToFill */
		$objectToFill = parent::toObject($objectToFill);

		$objectClassName = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $this->scopeObjectType);
		$peerClass = $objectClassName.'Peer';
		$objectId = $this->objectId;
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
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->objectId);

		return $objectToFill;
	}
}
