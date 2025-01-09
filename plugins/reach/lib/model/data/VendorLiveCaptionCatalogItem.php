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
		$latestEvent = null;
		if($object instanceof LiveEntry && $object->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY))
		{
			$events = ScheduleEventPeer::retrieveByTemplateEntryIdAndTypes($object->getId(), [ScheduleEventType::LIVE_STREAM]);

			if (count($events)) {
				$latestEvent = $events[count($events) - 1];
			}
		}
		elseif ($object instanceof LiveStreamScheduleEvent)
		{
			$latestEvent = $object;
		}

		if($latestEvent)
		{
			$data = new kScheduledVendorTaskData();
			$data->setEntryDuration($latestEvent->getDuration()*1000);
			$data->setStartDate(intval($latestEvent->getStartDate()));
			$data->setEndDate(intval($latestEvent->getEndDate()));
			$data->setScheduledEventId($latestEvent->getId());

			return $data;
		}

		return null;
	}
}
