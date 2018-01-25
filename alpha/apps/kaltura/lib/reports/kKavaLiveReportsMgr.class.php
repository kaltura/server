<?php

class kKavaLiveReportsMgr extends kKavaBase
{
	const MIN_RESULTS = 1000;
	const MAX_RESULTS = 10000;
	const MAX_LIVE_ENTRIES = 1000;
	const PEAK_AUDIENCE_MAX_BUCKETS = 1000;
	
	// intermediate metrics
	const METRIC_VIEW_COUNT = 'viewCount';
	const METRIC_BITRATE_COUNT = 'bitrateCount';
	
	// output fields
	const OUTPUT_ENTRY_ID = 'entryId';
	const OUTPUT_TIMESTAMP = 'timestamp';
	const OUTPUT_CITY_NAME = 'cityName';
	const OUTPUT_REGION_NAME = 'regionName';
	const OUTPUT_COUNTRY_NAME = 'countryName';
	const OUTPUT_SEC_VIEWED = 'secondsViewed';
	const OUTPUT_AUDIENCE = 'audience';
	const OUTPUT_DVR_AUDIENCE = 'dvrAudience';
	const OUTPUT_PEAK_AUDIENCE = 'peakAudience';
	const OUTPUT_PEAK_DVR_AUDIENCE = 'peakDvrAudience';
	const OUTPUT_AVG_BITRATE = 'avgBitrate';
	const OUTPUT_BUFFER_TIME = 'bufferTime';
	const OUTPUT_PLAYS = 'plays';
	const OUTPUT_REFERRER = 'referrer';
		
	protected static function getLimit($limit)
	{
		return max(min($limit, self::MAX_RESULTS), self::MIN_RESULTS);
	}
	
	protected static function getLiveEntries($partnerId, $entryIds, $isLive)
	{
		$filter = new entryFilter();
		$filter->setTypeEquel(entryType::LIVE_STREAM);
		$filter->setPartnerSearchScope($partnerId);
		if ($entryIds)
		{
			$filter->setIdIn($entryIds);
		}
		if (!is_null($isLive))
		{
			$filter->setIsLive($isLive);
		}
		
		$criteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$criteria->addAscendingOrderByColumn(entryPeer::NAME);		// Note: don't really care about order here, this is a hack to force the query to go to sphinx
		$criteria->setLimit(self::MAX_LIVE_ENTRIES);
		$filter->attachToCriteria($criteria);
	
		$criteria->applyFilters();
		return $criteria->getFetchedIds();
	}
	
	protected static function sortByEntryName($input)
	{
		if (!$input)
		{
			return $input;
		}
		
		$criteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$criteria->add(entryPeer::ID, array_keys($input), Criteria::IN);
		$criteria->add(entryPeer::TYPE, entryType::LIVE_STREAM);
		$criteria->addAscendingOrderByColumn(entryPeer::NAME);
		$criteria->applyFilters();
		$orderedIds = $criteria->getFetchedIds();
		
		$result = array();
		foreach ($orderedIds as $id)
		{
			$result[] = $input[$id];
		}
		return $result;
	}
	
	// filters
	protected static function getBaseFilter($partnerId, $eventTypes, $filter)
	{
		$result = array(
			self::getInFilter(self::DIMENSION_PLAYBACK_TYPE, array(
				self::PLAYBACK_TYPE_LIVE, 
				self::PLAYBACK_TYPE_DVR)),
		);

		// Note: the code assumes only view/play events exist in the realtime datasource
		//		if this changes, the below will need to be updated 
		if ($eventTypes)
		{
			$result[] = self::getInFilter(self::DIMENSION_EVENT_TYPE, $eventTypes);
		}
		
		$entryIds = array();
		if ($filter->entryIds)
		{
			$entryIds = explode(',', $filter->entryIds);
		}
		
		if ($entryIds || !is_null($filter->live))
		{
			$entryIds = self::getLiveEntries($partnerId, $entryIds, $filter->live);
			if (!$entryIds)
			{
				throw new kKavaNoResultsException();
			}
			
			// Note: since the entry ids where already filtered by partner, 
			//		we can skip filtering by partnerId in druid
			$result[] = self::getInFilter(self::DIMENSION_ENTRY_ID, $entryIds);
		}
		else
		{
			$result[] = self::getSelectorFilter(self::DIMENSION_PARTNER_ID, strval($partnerId));
		}
		
		return self::getAndFilter($result);
	}
	
	protected static function getFilterIntervals($filter)
	{
		$fromTime = $filter->fromTime;
		$toTime = $filter->toTime + self::VIEW_EVENT_INTERVAL;
		return self::getIntervals($fromTime, $toTime);
	}
	
	protected static function getViewEventPlaybackTypeFilter($playbackType)
	{
		return self::getAndFilter(array(
			self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_VIEW),
			self::getSelectorFilter(self::DIMENSION_PLAYBACK_TYPE, $playbackType),
		));
	}

	protected static function getViewEventHasBitrateFilter()
	{
		return self::getAndFilter(array(
				self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_VIEW),
				self::getSelectorFilter(self::DIMENSION_HAS_BITRATE, '1'),
		));
	}
	
	// base queries
	protected static function getBaseTimeseriesQuery($partnerId, $filter, $eventTypes)
	{
		return array(
			self::DRUID_QUERY_TYPE => self::DRUID_TIMESERIES,
			self::DRUID_DATASOURCE => self::REALTIME_DATASOURCE,
			self::DRUID_INTERVALS => self::getFilterIntervals($filter),
			self::DRUID_FILTER => self::getBaseFilter($partnerId, $eventTypes, $filter),
		);
	}
	
	protected static function getBaseTopNQuery($partnerId, $filter, $eventTypes, $dimension, $metric, $threshold)
	{
		return array(
			self::DRUID_QUERY_TYPE => self::DRUID_TOPN,
			self::DRUID_DATASOURCE => self::REALTIME_DATASOURCE,
			self::DRUID_INTERVALS => self::getFilterIntervals($filter),
			self::DRUID_FILTER => self::getBaseFilter($partnerId, $eventTypes, $filter),
			self::DRUID_DIMENSION => $dimension,
			self::DRUID_METRIC => $metric,
			self::DRUID_THRESHOLD => $threshold,
		);
	}

	protected static function getBaseGroupByQuery($partnerId, $filter, $eventTypes, $dimensions)
	{
		return array(
			self::DRUID_QUERY_TYPE => self::DRUID_GROUP_BY,
			self::DRUID_DATASOURCE => self::REALTIME_DATASOURCE,
			self::DRUID_INTERVALS => self::getFilterIntervals($filter),
			self::DRUID_FILTER => self::getBaseFilter($partnerId, $eventTypes, $filter),
			self::DRUID_DIMENSIONS => $dimensions,
		);
	}
	
	// aggregators
	protected static function getPlayCountAggregator()
	{
		return self::getFilteredAggregator(
			self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_PLAY),
			self::getLongSumAggregator(self::OUTPUT_PLAYS, self::METRIC_COUNT)
		);
	}
	
	protected static function getViewCountAggregator()
	{
		return self::getFilteredAggregator(
			self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_VIEW),
			self::getLongSumAggregator(self::METRIC_VIEW_COUNT, self::METRIC_COUNT)
		);
	}

	protected static function getAudienceAggregator() 
	{
		return self::getFilteredAggregator(
			self::getViewEventPlaybackTypeFilter(self::PLAYBACK_TYPE_LIVE),
			self::getLongSumAggregator(self::OUTPUT_AUDIENCE, self::METRIC_COUNT)
		);
	}

	protected static function getDvrAudienceAggregator()
	{
		return self::getFilteredAggregator(
			self::getViewEventPlaybackTypeFilter(self::PLAYBACK_TYPE_DVR),
			self::getLongSumAggregator(self::OUTPUT_DVR_AUDIENCE, self::METRIC_COUNT)
		);
	}
	
	protected static function getBitrateCountAggregator()
	{
		return self::getFilteredAggregator(
			self::getViewEventHasBitrateFilter(),
			self::getLongSumAggregator(self::METRIC_BITRATE_COUNT, self::METRIC_COUNT)
		);
	}
	
	protected static function getBufferTimeAggregator()
	{
		return self::getFilteredAggregator(
			self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_VIEW),
			self::getDoubleSumAggregator(self::METRIC_BUFFER_TIME_SUM, self::METRIC_BUFFER_TIME_SUM)
		);
	}
	
	protected static function getBitrateSumAggregator()
	{
		return self::getFilteredAggregator(
			self::getViewEventHasBitrateFilter(),
			self::getDoubleSumAggregator(self::METRIC_BITRATE_SUM, self::METRIC_BITRATE_SUM)
		);
	}
	
	protected static function addBaseAggregations(&$query)
	{
		$query[self::DRUID_AGGR] = array(
			self::getPlayCountAggregator(),
			self::getViewCountAggregator(),
			self::getAudienceAggregator(),
			self::getDvrAudienceAggregator(),
			self::getBufferTimeAggregator(),
			self::getBitrateCountAggregator(),
			self::getBitrateSumAggregator(),
		);
		
		$query[self::DRUID_POST_AGGR] = array(
			self::getArithmeticPostAggregator(self::OUTPUT_AVG_BITRATE, '/', array(
				self::getFieldAccessPostAggregator(self::METRIC_BITRATE_SUM),
				self::getFieldAccessPostAggregator(self::METRIC_BITRATE_COUNT),
			)),
			self::getArithmeticPostAggregator(self::OUTPUT_BUFFER_TIME, '/', array(
				self::getFieldAccessPostAggregator(self::METRIC_BUFFER_TIME_SUM),
				self::getFieldAccessPostAggregator(self::METRIC_VIEW_COUNT),
			)),
		);
	}
	
	protected static function updateBaseFields(&$dest, $src)
	{
		$fieldNames = array(
			self::OUTPUT_PLAYS,
			self::OUTPUT_AVG_BITRATE, 
			self::OUTPUT_AUDIENCE, 
			self::OUTPUT_DVR_AUDIENCE);
		
		foreach ($fieldNames as $fieldName)
		{
			$dest[$fieldName] = $src[$fieldName];
		}
		$dest[self::OUTPUT_BUFFER_TIME] = $src[self::OUTPUT_BUFFER_TIME] * 6;	// return in minutes
		$dest[self::OUTPUT_SEC_VIEWED] = $src[self::METRIC_VIEW_COUNT] * self::VIEW_EVENT_INTERVAL;
	}
	
	// reports
	public static function partnerTotal($partnerId, $filter, $limit)
	{
		// view events
		$query = self::getBaseTimeseriesQuery($partnerId, $filter, null);
		self::addBaseAggregations($query);
		$item = self::runGranularityAllQuery($query);
				
		$result = array();
		self::updateBaseFields($result, $item);

		return array($result);
	}
		
	public static function entryTotal($partnerId, $filter, $limit)
	{
		// view events
		$query = self::getBaseTopNQuery(
			$partnerId, 
			$filter, 
			null, 
			self::DIMENSION_ENTRY_ID, 
			self::METRIC_VIEW_COUNT,
			self::getLimit($limit));
		self::addBaseAggregations($query);
		$queryResult = self::runGranularityAllQuery($query);
		
		$result = array();
		foreach ($queryResult as $item)
		{
			$output = array();
			$entryId = $item[self::DIMENSION_ENTRY_ID];
			$output[self::OUTPUT_ENTRY_ID] = $entryId;
			$output[self::OUTPUT_PEAK_AUDIENCE] = 0;		// calculated below
			$output[self::OUTPUT_PEAK_DVR_AUDIENCE] = 0;	// calculated below
			self::updateBaseFields($output, $item);
			$result[$entryId] = $output;
		} 
		
		// peak audience
		$query = self::getBaseTopNQuery(
			$partnerId, 
			$filter, 
			array(self::EVENT_TYPE_VIEW), 
			self::DIMENSION_ENTRY_ID, 
			self::METRIC_VIEW_COUNT,
			self::getLimit($limit));
		$query[self::DRUID_AGGR] = array(
			self::getViewCountAggregator(),
			self::getAudienceAggregator(),
			self::getDvrAudienceAggregator(),
		);
		
		$bucketSize = intval(($filter->toTime - $filter->fromTime) / 
				(self::PEAK_AUDIENCE_MAX_BUCKETS * self::VIEW_EVENT_INTERVAL));
		$bucketSize = max($bucketSize, 1);
		$period = 'PT' . ($bucketSize * self::VIEW_EVENT_INTERVAL) . 'S';
		
		$queryResult = self::runGranularityPeriodQuery($query, $period);
		
		$fieldMapping = array(
			self::OUTPUT_AUDIENCE => self::OUTPUT_PEAK_AUDIENCE, 
			self::OUTPUT_DVR_AUDIENCE => self::OUTPUT_PEAK_DVR_AUDIENCE
		);
		foreach ($queryResult as $timeResult)
		{
			foreach ($timeResult[self::DRUID_RESULT] as $entryResult)
			{
				$entryId = $entryResult[self::DIMENSION_ENTRY_ID];
				if (!isset($result[$entryId]))
				{
					continue;
				}
				
				foreach ($fieldMapping as $src => $dest)
				{
					$value = intval($entryResult[$src] / $bucketSize);
					if ($value > $result[$entryId][$dest])
					{
						$result[$entryId][$dest] = $value;
					}  
				}
			}
		}
		
		$result = self::sortByEntryName($result);
		
		return $result;
	}
	
	public static function entrySyndicationTotal($partnerId, $filter, $limit)
	{
		$query = self::getBaseTopNQuery(
			$partnerId, 
			$filter, 
			array(self::EVENT_TYPE_PLAY), 
			self::DIMENSION_URL, 
			self::OUTPUT_PLAYS,
			self::getLimit($limit));
		$query[self::DRUID_AGGR] = array(
			self::getPlayCountAggregator(),
		);
		
		$queryResult = self::runGranularityAllQuery($query);
		
		$result = array();
		foreach ($queryResult as $item)
		{
			$output = array();
			$output[self::OUTPUT_REFERRER] = $item[self::DIMENSION_URL];
			$output[self::OUTPUT_PLAYS] = $item[self::OUTPUT_PLAYS];
			$result[] = $output;
		}
		
		return $result;
	}
	
	public static function entryTimeline($partnerId, $filter, $limit)
	{
		$query = self::getBaseTimeseriesQuery(
			$partnerId, 
			$filter, 
			array(self::EVENT_TYPE_VIEW));
		$query[self::DRUID_CONTEXT] = array(
			self::DRUID_SKIP_EMPTY_BUCKETS => self::DRUID_TRUE
		);
		$query[self::DRUID_AGGR] = array(
			self::getAudienceAggregator(),
			self::getDvrAudienceAggregator(),
		);
		$queryResult = self::runGranularityPeriodQuery($query, self::VIEW_EVENT_PERIOD);
	
		$result = '';
		foreach ($queryResult as $input)
		{
			$event = $input[self::DRUID_RESULT];
			$output = self::parseTimestamp($input[self::DRUID_TIMESTAMP]) . ',' . 
				$event[self::OUTPUT_AUDIENCE] . ',' .
				$event[self::OUTPUT_DVR_AUDIENCE] . ';';
			$result .= $output;
		}

		return $result;
	}
	
	public static function entryGeoTimeline($partnerId, $filter, $limit)
	{
		$dimensions = array(
			self::DIMENSION_ENTRY_ID => self::OUTPUT_ENTRY_ID,
			self::DIMENSION_LOCATION_CITY => self::OUTPUT_CITY_NAME,
			self::DIMENSION_LOCATION_REGION => self::OUTPUT_REGION_NAME,
			self::DIMENSION_LOCATION_COUNTRY => self::OUTPUT_COUNTRY_NAME,
		);

		// execute the query
		$query = self::getBaseGroupByQuery(
			$partnerId, 
			$filter, 
			null, 
			array_keys($dimensions)
		);
		
		switch ($filter->orderBy)
		{
		case '-plays':
			$orderByField = self::OUTPUT_PLAYS;
			break;
			
		default:	// only other relevant option is -audience
			$orderByField = self::OUTPUT_AUDIENCE;
			break;
		}
		
		$query[self::DRUID_LIMIT_SPEC] = self::getDefaultLimitSpec(
			self::getLimit($limit), 
			array(self::getOrderByColumnSpec(
				$orderByField, 
				self::DRUID_DESCENDING, 
				self::DRUID_NUMERIC)
			));
		
		self::addBaseAggregations($query);
		
		$queryResult = self::runGranularityPeriodQuery($query, self::VIEW_EVENT_PERIOD);
		KalturaLog::log("Druid returned [" . count($queryResult) . "] rows");
	
		// format the results
		$result = array();
		foreach ($queryResult as $input)
		{
			$event = $input[self::DRUID_EVENT];
			$output = array();
			$output[self::OUTPUT_TIMESTAMP] = self::parseTimestamp(
				$input[self::DRUID_TIMESTAMP]);
			foreach ($dimensions as $src => $dest)
			{
				$output[$dest] = $event[$src];
			}
			self::updateBaseFields($output, $event);
			$result[] = $output;
		}
		
		return $result;
	}
}
