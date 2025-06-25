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

	private static $map_between_objects = array
	(
		'detailLevel',
		'instruction',
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
