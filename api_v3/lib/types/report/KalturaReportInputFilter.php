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
	 * Search keywords in onjects tags
	 * 
	 * @var bool
	 */
	public $searchInTags;	

	/**
	 * Search keywords in onjects admin tags
	 * 
	 * @var bool
	 * @deprecated
	 */
	public $searchInAdminTags;	
	
	/**
	 * Search onjects in specified categories
	 * 
	 * @var string
	 */
	public $categories;
	
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

	private static $map_between_objects = array
	(
		'keywords',
		'searchInTags' => 'search_in_tags',
		'searchInAdminTags' => 'search_in_admin_tags',
		'categories',
		'customVar1In' => 'custom_var1',
		'customVar2In' => 'custom_var2',
		'customVar3In' => 'custom_var3',
		'deviceIn' => 'devices',
		'countryIn' => 'countries',
		'regionIn' => 'regions',
		'operatingSystemFamilyIn' => 'os_families',
		'browserFamilyIn' => 'browsers_families',
		'timeZoneOffset',
		'interval',
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
