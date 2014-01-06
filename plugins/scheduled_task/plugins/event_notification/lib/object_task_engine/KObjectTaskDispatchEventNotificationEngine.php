<?php

/**
 * @package plugins.scheduledTaskEventNotification
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskDispatchEventNotificationEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaDispatchEventNotificationObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		$client = $this->getClient();
		$templateId = $objectTask->eventNotificationTemplateId;
		$eventNotificationPlugin = KalturaEventNotificationClientPlugin::get($client);
		$scope = new KalturaEventNotificationScope();
		$scope->objectId =$object->id;
		$scope->scopeObjectType = KalturaEventNotificationEventObjectType::ENTRY;
		$eventNotificationPlugin->eventNotificationTemplate->dispatch($templateId, $scope);
	}
}