<?php

require_once __DIR__ . '/AggregatorConfiguration.php';

/**
 *
 */
interface IAggregatorData
{
	/**
	 * @return AggregatorConfiguration
	 */
	public function getAggregatorConfig();

	/**
	 * @return IKalturaLogger
	*/
	public function getLogger();

	/**
	 * @return KalturaClient
	*/
	public function getClient();
}

/**
 *
 */
class EntriesAggregator implements IKalturaLogger, IAggregatorData
{
	const XPATH_TEMPLATE = "/*[local-name()='metadata']/*[local-name()='@FIELD_NAME@']";
	const COUNT_PLAYS = "count_plays";

	const ECT_MOVIE = 1;
	const ECT_SERIES = 2;
	const ECT_EPISODE = 3;

	private $entryContentTypeId;
	private $entryContentTypeValue;

	/**
	 * @var IAggregatorData
	 */
	protected $aggregatorData;

	/**
	 * @var KalturaMetadataClientPlugin
	 */
	private $metadataPlugin;

	private $numPlaysInScanPeriod;
	private $totalNumPlays;
	private $availableFromDate;

	/**
	 * Constructor
	 * @param IAggregatorData $aggregatorData
	 * @param int $entryContentTypeId ECT_* constants
	 */
	protected function __construct( IAggregatorData $aggregatorData, $entryContentTypeId )
	{
		$this->aggregatorData = $aggregatorData;

		$this->entryContentTypeId = $entryContentTypeId;
		$this->entryContentTypeValue = $this->getEntryContentTypeStringById( $entryContentTypeId );
		$this->metadataPlugin = KalturaMetadataClientPlugin::get( $this->getClient() );
	}

	/**
	 *
	 * @param int $id
	 */
	public function getEntryContentTypeStringById( $entryContentTypeId )
	{
		switch ( $entryContentTypeId )
		{
			case self::ECT_MOVIE:
				return $this->getAggregatorConfig()->getEntryContentTypeValueMovie();

			case self::ECT_SERIES:
				return $this->getAggregatorConfig()->getEntryContentTypeValueSeries();

			case self::ECT_EPISODE:
				return $this->getAggregatorConfig()->getEntryContentTypeValueEpisode();

			default:
				throw new Exception("Unknown entry content type id: {$entryContentTypeId}");
		}
	}

	public function getEntryContentTypeValue()
	{
		return $this->entryContentTypeValue;
	}
		
	/**
	 * (non-PHPdoc)
	 * @see IKalturaLogger::log()
	 */
	public function log($message)
	{
		$this->getLogger()->log( "EntriesAggregator[" . $this->getEntryContentTypeValue() ."] " . $message );
	}

	/**
	 * @return AggregatorConfiguration
	 */
	public function getAggregatorConfig()
	{
		return $this->aggregatorData->getAggregatorConfig();
	}

	/**
	 * @return IKalturaLogger
	 */
	public function getLogger()
	{
		return $this->aggregatorData->getLogger();
	}

	/**
	 * @return KalturaClient
	 */
	public function getClient()
	{
		return $this->aggregatorData->getClient();
	}

	/**
	 * @return KalturaBaseEntryFilter
	 */
	protected function getEntryFilter()
	{
		$filter = new KalturaBaseEntryFilter();

		$filter->advancedSearch = new KalturaMetadataSearchItem();
		$filter->advancedSearch->metadataProfileId = $this->getAggregatorConfig()->getEntryContentTypeMetadataProfileId();

		$filter->advancedSearch->items = array();
		$filter->advancedSearch->items[0] = new KalturaSearchCondition();
		$filter->advancedSearch->items[0]->field = self::getFullXpath( $this->getAggregatorConfig()->getEntryContentTypeMetadataFieldName() );
		$filter->advancedSearch->items[0]->value = $this->getEntryContentTypeValue();

		return $filter;
	}

	public function getNumPlaysInScanPeriod() { return $this->numPlaysInScanPeriod; }
	protected function setNumPlaysInScanPeriod( $value ) { $this->numPlaysInScanPeriod = $value; }

	public function getTotalNumPlays() { return $this->totalNumPlays; }
	protected function setTotalNumPlays( $value  ) { $this->totalNumPlays = $value; }

	public function getAvailableFromDate() { return $this->availableFromDate; }
	protected function setAvailableFromDate( $value ) { $this->availableFromDate = $value; }

	public function onPreProcessHandler()  {}
	public function onPostProcessHandler()  {}

	public function onPreProcessEntry( $entry )  {}

	public function onPostProcessEntry( $entry )
	{
		$this->updateEntryStatistics( $entry );
	}

	/**
	 *
	 * @param KalturaBaseEntry $entry
	 * @return int num plays
	 */
	public function onProcessEntry( $entry )
	{
		$this->log( "onProcessEntry [" . $this->getEntryContentTypeValue() . "] - {$entry->name}" );

		$this->numPlaysInScanPeriod = $this->getNumPlaysInScanPeriodFromReportService( $entry );

		if ( isset($entry->plays) )
		{
			$this->totalNumPlays = $entry->plays;
		}
		else
		{
			throw new Exception("entry->plays is not defined");
		}

		$this->availableFromDate = max( $entry->createdAt, (int)$entry->startDate );
	}

	/**
	 * @param EntriesAggregator $entriesAggregator
	 */
	public static function processEntries( $entriesAggregator )
	{
		$entriesAggregator->onPreProcessHandler();

		$entriesFilter = $entriesAggregator->getEntryFilter();

		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;

		$lastUpdatedAt = 0;
		$alreadyProcessedEntryIds = "";

		$done = false;
		while ( ! $done )
		{
			// Prepare page filter (due to potential mass of entries, we'll use
			// a descending updatedAt filter approach instead of incrementing the pageIndex.
			$entriesFilter->orderBy = "-updatedAt"; // KalturaBaseEntryOrderBy.UPDATED_AT_DESC

			if ( $lastUpdatedAt != 0 ) {
				$entriesFilter->updatedAtLessThanOrEqual = $lastUpdatedAt;
			}

			if ( $alreadyProcessedEntryIds != "" ) {
				$entriesFilter->idNotIn = $alreadyProcessedEntryIds;
			}

			// Fetch entries
			$result = $entriesAggregator->getClient()->baseEntry->listAction($entriesFilter, $pager);

			$receivedObjectsCount = count($result->objects);
			if ( $receivedObjectsCount > 0 )
			{
				// Process fetched entries and update filter vars for next iteration
				foreach ( $result->objects as $entry )
				{
					// Process a single entry
					$entriesAggregator->onPreProcessEntry( $entry );
					$entriesAggregator->onProcessEntry( $entry );
					$entriesAggregator->onPostProcessEntry( $entry );

					// Update filter data
					if ($lastUpdatedAt != $entry->updatedAt)	{ $alreadyProcessedEntryIds = ""; } // clear the last ids, the entries will not be returned anyway due to the updatedAt <= condition
					if ($alreadyProcessedEntryIds != "")		{ $alreadyProcessedEntryIds += ","; }
					$alreadyProcessedEntryIds += $entry->id;

					$lastUpdatedAt = $entry->updatedAt;
				}
			}
			else
			{
				$done = true;
			}
		}

		$entriesAggregator->onPostProcessHandler();
	}

	private function getNumPlaysInScanPeriodFromReportService( $entry )
	{
		$numPlays = 0;

		$reportType = KalturaReportType::TOP_CONTENT;
		$reportInputFilter = new KalturaReportInputFilter();
		$objectIds = $entry->id;

		$reportInputFilter->fromDate = $this->getAggregatorConfig()->getFilterStartTime();
		$reportInputFilter->toDate = $this->getAggregatorConfig()->getFilterEndTime();

		$result = $this->getClient()->report->getTotal($reportType, $reportInputFilter, $objectIds);
		$header = explode( ',', $result->header );
		$data = explode( ',', $result->data );

		$idx = array_search(self::COUNT_PLAYS, $header);
		if ( $idx !== false )
		{
			$numPlays = (int)$data[$idx];
		}

		return $numPlays;
	}

	/**
	 * For a given FIELD_NAME, compose an xpath in the format: /*[local-name()='metadata']/*[local-name()='@FIELD_NAME@']
	 *
	 * @param string $fieldName
	 * @return string
	 */
	public static function getFullXpath( $fieldName )
	{
		return str_replace("@FIELD_NAME@", $fieldName, self::XPATH_TEMPLATE );
	}

	protected function updateEntryStatistics( $entry )
	{
		// Prepare metadata vars
		$statisticsMetadataProfileId = $this->getAggregatorConfig()->getStatisticsMetadataProfileId();
		$objectType = KalturaMetadataObjectType::ENTRY;
		$objectId = $entry->id;

		$totalNumPlaysMetadataFieldName = $this->getAggregatorConfig()->getTotalNumPlaysMetadataFieldName();
		$numPlaysInScanPeriodMetadataFieldName = $this->getAggregatorConfig()->getNumPlaysInScanPeriodMetadataFieldName();
		$availableFromDateMetadataFieldName = $this->getAggregatorConfig()->getAvailableFromDateMetadataFieldName();

		// Look for an existing metadata entry to update, or add a new one
		$metadata = $this->getMetadata( $statisticsMetadataProfileId, $entry->id );
		if ( $metadata ) // Data exists
		{
			$xml = simplexml_load_string( $metadata->xml );
		}
		else
		{
			$xml = new SimpleXMLElement('<metadata/>');
		}

		$xml->$totalNumPlaysMetadataFieldName = (int)$this->getTotalNumPlays();
		$xml->$numPlaysInScanPeriodMetadataFieldName = (int)$this->getNumPlaysInScanPeriod();
		$xml->$availableFromDateMetadataFieldName = (int)$this->getAvailableFromDate();

		$xmlData = $xml->asXML();

		if ( $metadata ) // Data exists
		{
			$metadataId = $metadata->id;
			$version = null;
			$result = $this->metadataPlugin->metadata->update($metadataId, $xmlData, $version);
		}
		else
		{
			$result = $this->metadataPlugin->metadata->add($statisticsMetadataProfileId, $objectType, $objectId, $xmlData);
		}

		if ( $result && isset( $result->error ) )
		{
			throw new Exception( "Can't set/update metadata for entry {$entry->id}: " . $result->error );
		}
	}

	protected function getMetadata( $metadataProfileId, $entryId )
	{
		$metadata = null;

		$filter = new KalturaMetadataFilter();
		$filter->partnerIdEqual = $this->getAggregatorConfig()->getPartnerId();
		$filter->metadataProfileIdEqual = $metadataProfileId;
		$filter->metadataObjectTypeEqual = KalturaMetadataObjectType::ENTRY;
		$filter->objectIdEqual = $entryId;

		$result = $this->metadataPlugin->metadata->listAction($filter);
		if ( count( $result->objects ) > 0 )
		{
			$metadata = $result->objects[0];
		}

		return $metadata;
	}
}

/**
 *
 */
class MoviesAggregator extends EntriesAggregator
{
	public function __construct( IAggregatorData $aggregatorData )
	{
		parent::__construct( $aggregatorData, self::ECT_MOVIE );
	}
}

/**
 *
 */
class SeriesAggregator extends EntriesAggregator
{
	public function __construct( IAggregatorData $aggregatorData )
	{
		parent::__construct( $aggregatorData, self::ECT_SERIES );
	}

	public function onProcessEntry( $seriesEntry )
	{
		$this->log( "onProcessEntry [" . $this->getEntryContentTypeValue() . "] - {$seriesEntry->name}" );

		$seriesCategoryId = null;

		$filter = new KalturaCategoryEntryFilter();
		$filter->entryIdEqual = $seriesEntry->id;
		$result = $this->getClient()->categoryEntry->listAction($filter);
		$numCategories = count( $result->objects );
		if ( $numCategories == 1 )
		{
			$seriesCategoryId = $result->objects[0]->categoryId;
		}
		elseif ( $numCategories > 1 )
		{
			$catIds = array();
			foreach ( $result->objects as $result )
			{
				$catIds[] = $result->categoryId;
			}

			// Amont the series's categories, find the one that's under the Series root.
			$filter = new KalturaCategoryFilter();
			$filter->idIn = implode( ',', $catIds );
			$filter->ancestorIdIn = $this->getAggregatorConfig()->getSeriesRootCategoryId();
			$result = $this->getClient()->category->listAction($filter);
			$seriesCategoryId = $result->objects[0]->id;
		}

		if ( $seriesCategoryId === null )
		{
			throw new Exception("Can't determine root category for series entry {$seriesEntry->id}: [{$seriesEntry->categoriesIds}]");
		}

		$episodesAggregator = new EpisodesAggregator( $this->aggregatorData, $seriesCategoryId );
		EntriesAggregator::processEntries( $episodesAggregator );

		// Set the series's statistics according to all episodes accumulated/digested date
		$this->setTotalNumPlays( $episodesAggregator->getAccumulatedTotalNumPlays() );
		$this->setNumPlaysInScanPeriod( $episodesAggregator->getAccumulatedTotalNumPlaysInScanPeriod() );
		$this->setAvailableFromDate( $episodesAggregator->getLatestAvailableFromDate() );
	}
}

/**
 *
 */
class EpisodesAggregator extends EntriesAggregator
{
	private $seriesCategoryId;
	private $accumulatedNumPlaysInScanPeriod;
	private $accumulatedTotalNumPlays;
	private $latestAvailableFromDate;

	public function __construct( IAggregatorData $aggregatorData, $seriesCategoryId )
	{
		parent::__construct( $aggregatorData, self::ECT_EPISODE );
		$this->seriesCategoryId = $seriesCategoryId;
	}

	public function getAccumulatedTotalNumPlaysInScanPeriod()
	{
		return $this->accumulatedNumPlaysInScanPeriod;
	}

	public function getAccumulatedTotalNumPlays()
	{
		return $this->accumulatedTotalNumPlays;
	}

	public function getLatestAvailableFromDate()
	{
		return $this->latestAvailableFromDate;
	}

	/**
	 * @return KalturaBaseEntryFilter
	 */
	protected function getEntryFilter()
	{
		$filter = parent::getEntryFilter();
		$filter->categoryAncestorIdIn = $this->seriesCategoryId;

		return $filter;
	}

	public function onPreProcessHandler()
	{
		$this->accumulatedNumPlaysInScanPeriod = 0;
		$this->accumulatedTotalNumPlays = 0;
		$this->latestAvailableFromDate = 0;
	}

	public function onProcessEntry( $episodeEntry )
	{
		parent::onProcessEntry( $episodeEntry );

		$this->accumulatedTotalNumPlays += $this->getTotalNumPlays();
		$this->accumulatedNumPlaysInScanPeriod += $this->getNumPlaysInScanPeriod();

		$this->latestAvailableFromDate = max( $this->latestAvailableFromDate, $this->getAvailableFromDate() );
	}
}

/**
 *
 */
class EntryStatisticsAggregator implements IKalturaLogger, IAggregatorData
{
	private $aggregatorConfig;
	private $client;

	/**
	 * Class entry point method.
	 * @param AggregatorConfiguration $aggregatorConfig
	 */
	public function run( AggregatorConfiguration $aggregatorConfig )
	{
		try
		{
			$this->log( "--------------------------------------------------------------" );
			$this->log( "AggregatorConfiguration: " . print_r( $aggregatorConfig, true ) );
			$this->aggregatorConfig = $aggregatorConfig;
			$this->client = $this->createClient();

			// Process Movies
			EntriesAggregator::processEntries( new MoviesAggregator( $this ) );

			// Process Series, which in turn will process related episodes
			EntriesAggregator::processEntries( new SeriesAggregator( $this ) );
		}
		catch ( Exception $e )
		{
			$this->log( $e->getMessage() );
			throw $e;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see IKalturaLogger::log()
	 */
	public function log($message)
	{
		echo date('Y-m-d H:i:s') . ' ' .  $message . "\n";
	}

	/**
	 * @return KalturaClient
	 * @throws Exception
	 */
	private function createClient()
	{
		$partnerId = $this->aggregatorConfig->getPartnerId();

		$kConfig = new KalturaConfiguration( $partnerId );
		$kConfig->serviceUrl = $this->aggregatorConfig->getServiceUrl();
		$kConfig->setLogger($this);

		$client = new KalturaClient($kConfig);

		try
		{
			$ks = $client->generateSession(
								$this->aggregatorConfig->getAdminSecret(),
								$this->aggregatorConfig->getUserId(),
								KalturaSessionType::ADMIN,
								$partnerId
							);

			$client->setKs($ks);
		}
		catch(Exception $ex)
		{
			throw new Exception("could not create client - {$ex->getMessage()}");
		}

		return $client;
	}

	/**
	 * @return AggregatorConfiguration
	 */
	public function getAggregatorConfig()
	{
		return $this->aggregatorConfig;
	}

	/**
	 * @return IKalturaLogger
	*/
	public function getLogger()
	{
		return $this;
	}

	/**
	 * @return KalturaClient
	*/
	public function getClient()
	{
		return $this->client;
	}
}
