<?php

/**
 * @package plugins.scheduledTaskMetadata
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskExecuteMetadataXsltEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaExecuteMetadataXsltObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		$entryId = $object->id;
		$metadataProfileId = $objectTask->metadataProfileId;
		$metadataObjectType = $objectTask->metadataObjectType;
		$xslt = $objectTask->xslt;
		$client = $this->getClient();
		$metadataPlugin = KalturaMetadataClientPlugin::get($client);

		$filter = new KalturaMetadataFilter();
		$filter->objectIdEqual = $entryId;
		$filter->metadataProfileIdEqual = $metadataProfileId;
		$filter->metadataObjectTypeEqual = $metadataObjectType;
		$metadataResult = $metadataPlugin->metadata->listAction($filter);
		if (!count($metadataResult->objects))
		{
			KalturaLog::err(sprintf('Metadata object was not found for entry %s, profile id %s and object type %s', $entryId, $metadataProfileId, $metadataObjectType));
			return;
		}

		$metadataId = $metadataResult->objects[0]->id;
		$metadataPlugin->metadata->updateFromXSL($metadataId, $xslt);
	}
}