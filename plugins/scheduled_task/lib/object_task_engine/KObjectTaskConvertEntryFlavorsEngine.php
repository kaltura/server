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
		$reconvert = $objectTask->reconvert;

		$client = $this->getClient();
		$flavorParamsIds = explode(',', $objectTask->flavorParamsIds);
		foreach($flavorParamsIds as $flavorParamsId)
		{
			try
			{
				$flavorAssetFilter = new KalturaFlavorAssetFilter();
				$flavorAssetFilter->entryIdEqual = $entryId;
				$flavorAssetFilter->flavorParamsIdEqual = $flavorParamsId;
				$flavorAssetFilter->statusEqual = KalturaFlavorAssetStatus::READY;
				$flavorAssetResponse = $client->flavorAsset->listAction($flavorAssetFilter);
				if (!count($flavorAssetResponse->objects) || $reconvert)
					$client->flavorAsset->convert($entryId, $flavorParamsId);

			}
			catch(Exception $ex)
			{
				KalturaLog::err(sprintf('Failed to convert entry id %s with flavor params id %s', $entryId, $flavorParamsId));
				KalturaLog::err($ex);
			}
		}
	}
}