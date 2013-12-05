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
		$flavorParams = array_map('trim', explode(',', $objectTask->flavorParams));
		// remove empty elements that can be identified as flavor params 0
		$flavorParams = array_filter($flavorParams, create_function('$a','return strlen($a) > 0;'));
		foreach($flavorParams as $flavorParamId)
		{
			try
			{
				$this->impersonate($object->partnerId);
				$client->flavorAsset->convert($entryId, $flavorParamId);
				$this->unimpersonate();
			}
			catch(Exception $ex)
			{
				KalturaLog::err(sprintf('Failed to convert entry id %s with flavor params id %s', $entryId, $flavorParamId));
				KalturaLog::err($ex);
			}
		}
	}
}