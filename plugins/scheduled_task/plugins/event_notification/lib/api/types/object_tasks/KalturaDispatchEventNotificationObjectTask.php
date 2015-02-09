<?php

/**
 * @package plugins.scheduledTaskEventNotification
 * @subpackage api.objects.objectTasks
 */
class KalturaDispatchEventNotificationObjectTask extends KalturaObjectTask
{
	/**
	 * The event notification template id to dispatch
	 *
	 * @var int
	 */
	public $eventNotificationTemplateId;

	public function __construct()
	{
		$this->type = ScheduledTaskEventNotificationPlugin::getApiValue(DispatchEventNotificationObjectTaskType::DISPATCH_EVENT_NOTIFICATION);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage()
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$this->validatePropertyNotNull('eventNotificationTemplateId');

		myPartnerUtils::addPartnerToCriteria('EventNotificationTemplate', kCurrentContext::getCurrentPartnerId(), true);
		$eventNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($this->eventNotificationTemplateId);
		if (is_null($eventNotificationTemplate))
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $this->eventNotificationTemplateId);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);
		$dbObject->setDataValue('eventNotificationTemplateId', $this->eventNotificationTemplateId);
		return $dbObject;
	}

	public function fromObject($srcObj, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($srcObj, $responseProfile);

		/** @var kObjectTask $srcObj */
		$this->eventNotificationTemplateId = $srcObj->getDataValue('eventNotificationTemplateId');
	}
}