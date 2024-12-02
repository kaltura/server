<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorVideoAnalysisCatalogItem extends VendorCatalogItem
{
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
}
