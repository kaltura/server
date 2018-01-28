<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUsersCsvJobData extends KalturaJobData
{

	/**
	 * The filter should return the list of users that need to be specified in the csv.
	 *
	 * @var KalturaUserFilter
	 */
	public $filter;



	/**
	 * The metadata profile we should look the xpath in
	 *
	 * @var int
	 */
	public $metadataProfileId;


	/**
	 * The xpath to look in the metadataProfileId  and the wanted csv field name
	 *
	 * @var KalturaCsvAdditionalFieldInfoArray
	 */
	public $additionalFields;


	private static $map_between_objects = array
	(
		'filter',
		'metadataProfileId',
		'additionalFields',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbData = null, $props_to_skip = array())
	{
		if(is_null($dbData))
			$dbData = new kUsersCsvJobData();

		return parent::toObject($dbData, $props_to_skip);
	}

}