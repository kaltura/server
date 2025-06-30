<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaMetadataEnrichmentVendorTaskData extends KalturaLocalizedVendorTaskData
{
	/**
	 * The level of detail for the metadata enrichment process.
	 *
	 * @insertonly
	 * @var string
	 */
	public $detailLevel;

	/**
	 * Instructions describing what should be taken into account during the metadata enrichment process.
	 *
	 * @insertonly
	 * @var string
	 */
	public $instruction;

	/**
	 * Indicates whether the metadata enrichment results should be automatically applied on the task entry.
	 * Default is false.
	 *
	 * @var bool
	 */
	public $shouldApply;

	/**
	 * Specifies how metadata fields should be applied during enrichment.
	 * If 'FILL_EMPTY_AND_OVERRIDE_LIST', use overrideFields to specify which fields to override.
	 *
	 * @var KalturaMetadataEnrichmentApplyMode
	 */
	public $applyMode;

	/**
	 * List of entry fields to override when applyMode is set to 'FILL_EMPTY_AND_OVERRIDE_LIST'.
	 *
	 * @var KalturaStringArray
	 */
	public $overrideFields;

	private static $map_between_objects = array
	(
		'detailLevel',
		'instruction',
		'shouldApply',
		'applyMode',
		'overrideFields',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject) {
			$dbObject = new kMetadataEnrichmentVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}
