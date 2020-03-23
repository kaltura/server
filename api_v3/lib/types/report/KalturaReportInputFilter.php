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
	 * Filter by operating system
	 *
	 * @var string
	 */
	public $operatingSystemIn;

	/**
	 * Filter by browser family
	 *
	 * @var string
	 */
	public $browserFamilyIn;

	/**
	 * Filter by browser
	 *
	 * @var string
	 */
	public $browserIn;

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
	public $ownerIdsIn;

	/**
	 * @var KalturaESearchEntryOperator
	 */
	public $entryOperator;

	/**
	 * Entry created at greater than or equal as Unix timestamp
	 * @var time
	 */
	public $entryCreatedAtGreaterThanOrEqual;

	/**
	 * Entry created at less than or equal as Unix timestamp
	 * @var time
	 */
	public $entryCreatedAtLessThanOrEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var string
	 */
	public $playbackTypeIn;

	/**
	 * filter by playback context ids
	 * 
	 * @var string
	 */
	public $playbackContextIdsIn;

	/**
	 * filter by root entry ids
	 *
	 * @var string
	 */
	public $rootEntryIdIn;

	/**
	 * filter by error code
	 * 
	 * @var string
	 */
	public $errorCodeIn;

	/**
	 * filter by player version
	 *
	 * @var string
	 */
	public $playerVersionIn;

	/**
	 * filter by isp
	 *
	 * @var string
	 */
	public $ispIn;

	/**
	 * filter by application version
	 *
	 * @var string
	 */
	public $applicationVersionIn;

	/**
	 * filter by node id
         *
         * @var string
         */
        public $nodeIdsIn;

	/**
	 * filter by categories ancestor
	 *
	 * @var string
	 */
	public $categoriesAncestorIdIn;
	 
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
		'operatingSystemIn' => 'os',
		'browserFamilyIn' => 'browsers_families',
		'browserIn' => 'browsers',
		'timeZoneOffset',
		'interval',
		'mediaTypeIn' => 'media_types',
		'sourceTypeIn' => 'source_types',
		'ownerIdsIn' => 'owners',
		'entryCreatedAtGreaterThanOrEqual' => 'gte_entry_created_at',
		'entryCreatedAtLessThanOrEqual' => 'lte_entry_created_at',
		'entryIdIn' => 'entries_ids',
		'playbackTypeIn' => 'playback_types',
		'playbackContextIdsIn' => 'playback_context_ids',
		'rootEntryIdIn' => 'root_entries_ids',
		'errorCodeIn' => 'event_var1',
		'playerVersionIn' => 'player_versions',
		'ispIn' => 'isp',
		'applicationVersionIn' => 'application_versions',
		'nodeIdsIn' => 'node_ids',
		'categoriesAncestorIdIn'=> 'categories_ancestor_ids',
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
		if (!$reportInputFilter)
		{
			$reportInputFilter = new reportsInputFilter();
		}

		if ($this->entryOperator)
		{
			$reportInputFilter->entry_operator = $this->entryOperator->toObject();
		}

		return parent::toReportsInputFilter($reportInputFilter);
	}
}
