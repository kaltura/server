<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorConfigSchedulingLiveCaptionCatalogItem extends VendorLiveCaptionCatalogItem
{
	const CUSTOM_DATA_START_TIME_BUFFER = 'startTimeBuffer';

	const CUSTOM_DATA_END_TIME_BUFFER = 'endTimeBuffer';


	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::LIVE_CAPTION_CONFIG_SCHEDULING);
	}

	public function getStartTimeBuffer()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_START_TIME_BUFFER);
	}

	public function setStartTimeBuffer($startTimeBuffer): void
	{
		$this->putInCustomData(self::CUSTOM_DATA_START_TIME_BUFFER, $startTimeBuffer);
	}

	public function getEndTimeBuffer()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_END_TIME_BUFFER);
	}

	public function setEndTimeBuffer($endTimeBuffer): void
	{
		$this->putInCustomData(self::CUSTOM_DATA_END_TIME_BUFFER, $endTimeBuffer);
	}

	public function getTaskJobData($object)
	{
		$data = parent::getTaskJobData($object);

		// This catalog item adds a buffer to the live captioning
		$data->setStartDate(intval($data->getStartDate()) - $this->getStartTimeBuffer());
		$data->setEndDate(intval($data->getEndDate()) + $this->getEndTimeBuffer());

		return $data;
	}

}
