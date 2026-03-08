<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveCaptionCatalogItem extends VendorLiveCatalogItem
{
	const CUSTOM_DATA_START_TIME_BUFFER = 'startTimeBuffer';

	const CUSTOM_DATA_END_TIME_BUFFER = 'endTimeBuffer';

	public function getStartTimeBuffer()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_START_TIME_BUFFER, null, 0);
	}

	public function setStartTimeBuffer($startTimeBuffer): void
	{
		$this->putInCustomData(self::CUSTOM_DATA_START_TIME_BUFFER, $startTimeBuffer);
	}

	public function getEndTimeBuffer()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_END_TIME_BUFFER, null, 0);
	}

	public function setEndTimeBuffer($endTimeBuffer): void
	{
		$this->putInCustomData(self::CUSTOM_DATA_END_TIME_BUFFER, $endTimeBuffer);
	}

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

		$data = new kScheduledVendorTaskData();
		if($latestEvent)
		{
			$data->setEntryDuration($latestEvent->getDuration()*1000);
			$data->setStartDate(intval($latestEvent->getStartDate(null)));
			$data->setEndDate(intval($latestEvent->getEndDate(null)));
			$data->setScheduledEventId($latestEvent->getId());
			if ($this->getStartTimeBuffer() || $this->getEndTimeBuffer())
			{
				$data->setStartDate(intval($data->getStartDate()) - $this->getStartTimeBuffer());
				$data->setEndDate(intval($data->getEndDate()) + $this->getEndTimeBuffer());
				$data->setEntryDuration(($latestEvent->getDuration() + $this->getStartTimeBuffer() + $this->getEndTimeBuffer())*1000);
			}
		}

		return $data;
	}
}
