<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskStorageExportEngine extends KObjectTaskEntryEngineBase
{

	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaStorageExportObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		$entryId = $object->id;
		$storageId = $objectTask->storageId;
		if (!$storageId)
			throw new Exception('Storage profile was not configured');

		KalturaLog::info("Submitting entry export for entry $entryId to remote storage $storageId");

		$client = $this->getClient();
		$client->baseEntry->export($entryId, $storageId);
	}
}