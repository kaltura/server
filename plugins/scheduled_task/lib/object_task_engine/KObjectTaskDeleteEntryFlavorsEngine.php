<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskDeleteEntryFlavorsEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaDeleteEntryFlavorsObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		$deleteType = $objectTask->deleteType;

		$flavorParamsIds = explode(',', $objectTask->flavorParamsIds);
		$client = $this->getClient();

		$pager = new KalturaFilterPager();
		$pager->pageSize = 500; // use max size, throw exception in case we got more than 500 flavors where pagination is not supported
		$filter = new KalturaFlavorAssetFilter();
		$filter->entryIdEqual = $object->id;
		try
		{
			$flavorsResponse = $client->flavorAsset->listAction($filter);
		}
		catch(Exception $ex)
		{
			throw $ex;
		}
		if ($flavorsResponse->totalCount > $pager->pageSize)
			throw new Exception('Too many flavors were found where pagination is not supported');

		$flavors = $flavorsResponse->objects;
		KalturaLog::info('Found '.count($flavors). ' flavors');
		if (!count($flavors))
			return;

		if ($this->shouldSkipStaticContent($deleteType, $object, $flavors))
		{
		    KalturaLog::notice("Entry ID: [$object->id] content is 'static' - skipping MR actions");
		    return;
		}

		KalturaLog::info('Delete type is '.$deleteType);
		switch($deleteType)
		{
			case KalturaDeleteFlavorsLogicType::DELETE_LIST:
				$this->deleteFlavorByList($flavors, $flavorParamsIds);
				break;
			case KalturaDeleteFlavorsLogicType::KEEP_LIST_DELETE_OTHERS:
				$this->deleteFlavorsKeepingConfiguredList($flavors, $flavorParamsIds);
				break;
			case KalturaDeleteFlavorsLogicType::DELETE_KEEP_SMALLEST:
				$this->deleteAllButKeepSmallest($flavors);
				break;
		}
	}

	/**
	 * @param $id
	 */
	protected function deleteFlavor($id, $partnerId)
	{
		$client = $this->getClient();
		try
		{
			$client->flavorAsset->delete($id);
			KalturaLog::info('Flavor id '.$id.' was deleted');
		}
		catch(Exception $ex)
		{
			KalturaLog::err($ex);
			KalturaLog::err('Failed to delete flavor id '.$id);
		}
	}

	protected function findSmallestFlavor($flavors)
	{
		/** @var KalturaFlavorAsset $smallestFlavor */
		$smallestFlavor = null;
		foreach($flavors as $flavor)
		{
			/** @var KalturaFlavorAsset $flavor */
			if ($flavor->status != KalturaFlavorAssetStatus::READY)
				continue;

			if (!$flavor->size) // flavor must have size
				continue;

			if (is_null($smallestFlavor) || $flavor->size < $smallestFlavor->size)
			{
				$smallestFlavor = $flavor;
			}
		}

		return $smallestFlavor;
	}

	/**
	 * @param $flavors
	 * @param $flavorParamsIds
	 */
	protected function deleteFlavorsKeepingConfiguredList(array $flavors, array $flavorParamsIds)
	{
		// make sure at least one flavor will be left from the configured list
		$atLeastOneFlavorWillBeLeft = false;
		foreach ($flavors as $flavor)
		{
			/** @var $flavor KalturaFlavorAsset */
			if ($flavor->status != KalturaFlavorAssetStatus::READY)
				continue;

			if (in_array($flavor->flavorParamsId, $flavorParamsIds))
			{
				$atLeastOneFlavorWillBeLeft = true;
				break;
			}
		}

		if (!$atLeastOneFlavorWillBeLeft)
		{
			KalturaLog::warning('No flavors will be left after deletion, cannot continue.');
			return;
		}

		foreach ($flavors as $flavor)
		{
			/** @var $flavor KalturaFlavorAsset */
			if (!in_array($flavor->flavorParamsId, $flavorParamsIds))
			{
				$this->deleteFlavor($flavor->id, $flavor->partnerId);
			}
		}
	}

	/**
	 * @param $flavors
	 * @param $flavorParams
	 */
	protected function deleteFlavorByList(array $flavors, array $flavorParams)
	{
		foreach ($flavors as $flavor)
		{
			/** @var $flavor KalturaFlavorAsset */
			if (in_array($flavor->flavorParamsId, $flavorParams))
			{
				$this->deleteFlavor($flavor->id, $flavor->partnerId);
			}
		}
	}

	protected function deleteAllButKeepSmallest(array $flavors)
	{
		$smallestFlavor = $this->findSmallestFlavor($flavors);
		if (is_null($smallestFlavor))
		{
			KalturaLog::warning('Smallest flavor was not found, cannot continue');
			return;
		}
		$this->deleteFlavorsKeepingConfiguredList($flavors, array($smallestFlavor->flavorParamsId));
	}
	
	/**
	 * @param KalturaMediaEntry $object
	 */
	protected function shouldSkipStaticContent($deleteType, $object, $flavors)
	{
		// if should 'keep_smallest' asset, no need to check 'static' content
		if ($deleteType == KalturaDeleteFlavorsLogicType::DELETE_KEEP_SMALLEST)
		{
			KalturaLog::notice("Entry ID: [$object->id] delete Type is: [$deleteType] - proceeding with MR actions");
			return false;
		}

		$baseSourceFlavor = null;
		foreach ($flavors as $flavor)
		{
			/* @var $flavor KalturaFlavorAsset */
			if($flavor->flavorParamsId == 0)
			{
				$baseSourceFlavor = $flavor;
				break;
			}
		}

		if($baseSourceFlavor)
		{
			KalturaLog::notice("Found source asset with flavor params id 0 [$baseSourceFlavor->id], proceeding with MR action");
			return false;
		}
		
		return $this->isStaticContent($object);
	}
	
	/**
	 * @param KalturaMediaEntry $object
	 */
	protected function isStaticContent($object)
	{
		if (!$object instanceof KalturaMediaEntry)
		{
			KalturaLog::notice("Entry object ID: [$object->id] is not of type 'KalturaMediaEntry' - cannot check if content is 'static'");
			return false;
		}
		
		// 'kaltura_capture' does not have admin tags and is only identified by sourceType
		if ($object->sourceType == KalturaSourceType::LECTURE_CAPTURE)
		{
			return true;
		}
		
		$staticContentAdminTags = $this->getStaticContentAdminTagsArr();
		$adminTags = explode(',', $object->adminTags);
		
		return count(array_intersect($staticContentAdminTags, $adminTags));
	}
	
	protected function getStaticContentAdminTagsArr()
	{
		// Until kBatchUtils::tryLoadKconfConfig is enhanced to support caching all params - we return a static list
		return array(
			'kalturaclassroom',
			'zoomentry',
			'kalturameeting',
			'expressrecorder',
			'msteams',
			'webexapi',
		);
	}
}
