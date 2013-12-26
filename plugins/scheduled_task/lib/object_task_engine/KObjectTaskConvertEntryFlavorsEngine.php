<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskConvertEntryFlavorsEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaConvertEntryFlavorsObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		$entryId = $object->id;

		$client = $this->getClient();
		$flavorParamsIds = explode(',', $objectTask->flavorParamsIds);
		foreach($flavorParamsIds as $flavorParamsId)
		{
			try
			{
				$this->impersonate($object->partnerId);
				$client->flavorAsset->convert($entryId, $flavorParamsId);
				$this->unimpersonate();
			}
			catch(Exception $ex)
			{
				KalturaLog::err(sprintf('Failed to convert entry id %s with flavor params id %s', $entryId, $flavorParamsId));
				KalturaLog::err($ex);
			}
		}
	}
}