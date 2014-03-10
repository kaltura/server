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
		try
		{
			$this->impersonate($object->partnerId);
			$metadataResult = $metadataPlugin->metadata->listAction($filter);
			$this->unimpersonate();
		}
		catch(Exception $ex)
		{
			$this->unimpersonate();
			throw $ex;
		}
		if (!count($metadataResult->objects))
		{
			KalturaLog::info(sprintf('Metadata object was not found for entry %s, profile id %s and object type %s', $entryId, $metadataProfileId, $metadataObjectType));
			return;
		}

		$xsltFilePath = sys_get_temp_dir().'/xslt_'.time(true).'.xslt';
		file_put_contents($xsltFilePath, $xslt);

		$metadataId = $metadataResult->objects[0]->id;
		try
		{
			$this->impersonate($object->partnerId);
			$metadataPlugin->metadata->updateFromXSL($metadataId, $xsltFilePath);
			$this->unimpersonate();
		}
		catch(Exception $ex)
		{
			$this->unimpersonate();
			throw $ex;
		}
		unlink($xsltFilePath);
	}
}