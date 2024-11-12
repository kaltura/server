<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveCaptionCatalogItem extends VendorLiveCatalogItem
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::LIVE_CAPTION);
	}

	public function getTaskJobData($object)
	{
		if($object instanceof LiveEntry && $object->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY))
		{
			$events = ScheduleEventPeer::retrieveByTemplateEntryIdAndTypes($object->getId(), [ScheduleEventType::LIVE_STREAM]);

			$data = new kScheduledVendorTaskData();
			if(count($events))
			{
				$latestEvent = $events[count($events)-1];
				$data->setEntryDuration($latestEvent->getDuration()*1000);
				$data->setStartDate($latestEvent->getStartDate());
				$data->setEndDate($latestEvent->getEndDate());
				$data->setScheduledEventId($latestEvent->getId());
			}

			return $data;
		}

		return null;
	}
}
