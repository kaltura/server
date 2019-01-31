<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaReportInputFilter extends KalturaReportInputBaseFilter 
{
	/**
	 * Search keywords to filter objects
	 * 
	 * @var string
	 */
	public $keywords;
	
	/**
	 * Search keywords in objects tags
	 * 
	 * @var bool
	 */
	public $searchInTags;	

	/**
	 * Search keywords in objects admin tags
	 * 
	 * @var bool
	 * @deprecated
	 */
	public $searchInAdminTags;	
	
	/**
	 * Search objects in specified categories
	 * 
	 * @var string
	 */
	public $categories;

	/**
	 * Search objects in specified category ids
	 *
	 * @var string
	 */
	public $categoriesIdsIn;

	/**
	 * Filter by customVar1
	 *
	 * @var string
	 */
	public $customVar1In;

	/**
	 * Filter by customVar2
	 *
	 * @var string
	 */
	public $customVar2In;

	/**
	 * Filter by customVar3
	 *
	 * @var string
	 */
	public $customVar3In;

	/**
	 * Filter by device
	 *
	 * @var string
	 */
	public $deviceIn;

	/**
	 * Filter by country
	 *
	 * @var string
	 */
	public $countryIn;

	/**
	 * Filter by region
	 *
	 * @var string
	 */
	public $regionIn;

	/**
	 * Filter by city
	 *
	 * @var string
	 */
	public $citiesIn;

	/**
	 * Filter by operating system family
	 *
	 * @var string
	 */
	public $operatingSystemFamilyIn;

	/**
	 * Filter by browser family
	 *
	 * @var string
	 */
	public $browserFamilyIn;

	/**
	 * Time zone offset in minutes
	 * 
	 * @var int
	 */
	public $timeZoneOffset = 0;
	
	/**
	 * Aggregated results according to interval
	 * 
	 * @var KalturaReportInterval
	 */
	public $interval;

	/**
	 * Filter by media types
	 *
	 * @var string
	 */
	public $mediaTypeIn;

	/**
	 * Filter by source types
	 *
	 * @var string
	 */
	public $sourceTypeIn;

	/**
	 * Filter by entry owner
	 *
	 * @var string
	 */
	public $ownerIn;


	private static $map_between_objects = array
	(
		'keywords',
		'searchInTags' => 'search_in_tags',
		'searchInAdminTags' => 'search_in_admin_tags',
		'categories',
		'categoriesIdsIn' => 'categoriesIds',
		'customVar1In' => 'custom_var1',
		'customVar2In' => 'custom_var2',
		'customVar3In' => 'custom_var3',
		'deviceIn' => 'devices',
		'countryIn' => 'countries',
		'regionIn' => 'regions',
		'citiesIn' => 'cities',
		'operatingSystemFamilyIn' => 'os_families',
		'browserFamilyIn' => 'browsers_families',
		'timeZoneOffset',
		'interval',
		'mediaTypeIn' => 'media_types',
		'sourceTypeIn' => 'source_types',
		'ownerIn' => 'owners'
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaReportInputBaseFilter::toReportsInputFilter()
	 */
	public function toReportsInputFilter($reportInputFilter = null)
	{
		return parent::toReportsInputFilter($reportInputFilter);
	}
}
