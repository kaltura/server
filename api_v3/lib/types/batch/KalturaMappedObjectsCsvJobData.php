<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaMappedObjectsCsvJobData extends KalturaExportCsvJobData
{
	/**
	 * The metadata profile we should look the xpath in
	 * @var int
	 */
	public $metadataProfileId;

	/**
	 * The xpath to look in the metadataProfileId  and the wanted csv field name
	 * @var KalturaCsvAdditionalFieldInfoArray
	 */
	public $additionalFields;
	
	/**
	 * Array of header names and their mapped user fields
	 * @var KalturaKeyValueArray
	 */
	public $mappedFields;
	
	/**
	 * @var KalturaExportToCsvOptions
	 */
	public $options;

	private static $map_between_objects = array
	(
		'metadataProfileId',
		'additionalFields',
		'mappedFields',
		'options',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}