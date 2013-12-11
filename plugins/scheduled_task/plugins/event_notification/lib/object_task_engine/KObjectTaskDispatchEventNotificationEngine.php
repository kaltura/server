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
		$data = new KalturaEventNotificationDispatchJobData();

		$objectProperties = get_object_vars($object);
		foreach($objectProperties as $property => $value)
		{
			// append only scalar variables and nulls
			if (!is_null($value) && !is_scalar($value))
				continue;

			$keyValue = new KalturaKeyValue();
			$keyValue->key = $property;
			$keyValue->value = (string)$value;
			$data->contentParameters[] = $keyValue;
		}
		$data->templateId = $templateId;
		$eventNotificationPlugin->eventNotificationTemplate->dispatch($templateId, $data);
	}
}