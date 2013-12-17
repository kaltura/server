<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskModifyCategoriesEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaModifyCategoriesObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		$client = $this->getClient();
		$entryCategories = explode(',', $object->categories);
		$taskCategories = array();
		foreach($objectTask->categories as $categoryString)
		{
			/** @var KalturaString $categoryString */
			$taskCategories[] = trim($categoryString->value);
		}

		if ($objectTask->addRemoveType == KalturaScheduledTaskAddOrRemoveType::ADD)
		{
			$entryCategories = array_merge($entryCategories, $taskCategories);
		}
		elseif ($objectTask->addRemoveType == KalturaScheduledTaskAddOrRemoveType::REMOVE)
		{
			if (count($taskCategories))
			{
				foreach($entryCategories as &$tmpCategory)
				{
					if (in_array($tmpCategory, $taskCategories))
						$tmpCategory = null;
				}
			}
			else
			{
				$entryCategories = array();
			}
		}

		$entryForUpdate = new KalturaBaseEntry();
		$entryForUpdate->categories = implode(',', $entryCategories);

		$client->baseEntry->update($object->id, $entryForUpdate);
	}
}