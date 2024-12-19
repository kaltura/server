<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorVideoAnalysisCatalogItem extends VendorCatalogItem
{
	const CUSTOM_DATA_VIDEO_ANALYSIS_TYPE = 'video_analysis_type';
	const CUSTOM_DATA_MAX_VIDEO_DURATION = 'max_video_duration';

	public function applyDefaultValues(): void
	{
		$this->setServiceFeature(VendorServiceFeature::VIDEO_ANALYSIS);
	}

	public function isDuplicateTask(entry $entry): bool
	{
		return false;
	}

	public function isEntryTypeSupported($type, $mediaType = null): bool
	{
		$supportedMediaTypes = [entry::ENTRY_MEDIA_TYPE_VIDEO];
		return $type === entryType::MEDIA_CLIP && in_array($mediaType, $supportedMediaTypes);
	}

	public function setVideoAnalysisType($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_VIDEO_ANALYSIS_TYPE, $v);
	}

	public function getVideoAnalysisType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_VIDEO_ANALYSIS_TYPE, null, true);
	}

	public function setMaxVideoDuration($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_MAX_VIDEO_DURATION, $v);
	}

	public function getMaxVideoDuration()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MAX_VIDEO_DURATION, null, true);
	}

	public function isEntryDurationExceeding(entry $entry)
	{
		if (!is_null($this->getMaxVideoDuration()))
		{
			return $entry->getDuration() > $this->getMaxVideoDuration();
		}
		return false;
	}
}
