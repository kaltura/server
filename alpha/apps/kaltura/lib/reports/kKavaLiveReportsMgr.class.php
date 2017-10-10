<?php

class kKavaLiveReportsMgr extends kKavaBase
{
	const MAX_RESULTS = 1000;
	
	// intermediate metrics
	const METRIC_VIEW_COUNT = 'viewCount';
	const METRIC_BITRATE_COUNT = 'bitrateCount';
	
	// output fields
	const OUTPUT_ENTRY_ID = 'entryId';
	const OUTPUT_TIMESTAMP = 'timestamp';
	const OUTPUT_CITY_NAME = 'cityName';
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
		
	protected static function getLiveNowEntries($partnerId, $isLive, $entryIds)
	{
		$filter = new entryFilter();
		$filter->setTypeEquel(entryType::LIVE_STREAM);
		$filter->setIsLive($isLive);
		$filter->setPartnerSearchScope($partnerId);
		if ($entryIds)
		{
			$filter->setIdIn($entryIds);
		}
		
		$criteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$criteria->addAscendingOrderByColumn(entryPeer::NAME);		// Note: don't really care about order here, this is a hack to force the query to go to sphinx 
		$filter->attachToCriteria($criteria);
	
		$criteria->applyFilters();
		return $criteria->getFetchedIds();
	}
	
	protected static function sortByEntryName($input)
	{
		$criteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$criteria->add(entryPeer::ID, array_keys($input), Criteria::IN);
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
		if (!$eventTypes)
		{
			$eventTypes = array(self::EVENT_TYPE_VIEW, self::EVENT_TYPE_PLAY);
		}
		
		$result = array(
			self::getSelectorFilter(self::DIMENSION_PARTNER_ID, strval($partnerId)),
			self::getInFilter(self::DIMENSION_EVENT_TYPE, $eventTypes),
			self::getInFilter(self::DIMENSION_PLAYBACK_TYPE, array(
				self::PLAYBACK_TYPE_LIVE, 
				self::PLAYBACK_TYPE_DVR)),
		);
		
		$entryIds = array();
		if ($filter->entryIds)
		{
			$entryIds = explode(',', $filter->entryIds);
		}
		
		if (!is_null($filter->live))
		{
			$entryIds = self::getLiveNowEntries($partnerId, $filter->live, $entryIds);
			if (!$entryIds)
			{
				throw new kKavaNoResultsException();
			}
		}
		
		if ($entryIds)
		{
			$result[] = self::getInFilter(self::DIMENSION_ENTRY_ID, $entryIds);
		}
		
		return self::getAndFilter($result);
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
			self::DRUID_INTERVALS => self::getIntervals($filter->fromTime, $filter->toTime),
			self::DRUID_FILTER => self::getBaseFilter($partnerId, $eventTypes, $filter),
		);
	}
	
	protected static function getBaseTopNQuery($partnerId, $filter, $eventTypes, $dimension, $metric)
	{
		return array(
			self::DRUID_QUERY_TYPE => self::DRUID_TOPN,
			self::DRUID_DATASOURCE => self::REALTIME_DATASOURCE,
			self::DRUID_INTERVALS => self::getIntervals($filter->fromTime, $filter->toTime),
			self::DRUID_FILTER => self::getBaseFilter($partnerId, $eventTypes, $filter),
			self::DRUID_DIMENSION => $dimension,
			self::DRUID_METRIC => $metric,
			self::DRUID_THRESHOLD => self::MAX_RESULTS,
		);
	}

	protected static function getBaseGroupByQuery($partnerId, $filter, $eventTypes, $dimensions)
	{
		return array(
			self::DRUID_QUERY_TYPE => self::DRUID_GROUP_BY,
			self::DRUID_DATASOURCE => self::REALTIME_DATASOURCE,
			self::DRUID_INTERVALS => self::getIntervals($filter->fromTime, $filter->toTime),
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
			self::OUTPUT_DVR_AUDIENCE, 
			self::OUTPUT_BUFFER_TIME);
		
		foreach ($fieldNames as $fieldName)
		{
			$dest[$fieldName] = $src[$fieldName];
		}
		$dest[self::OUTPUT_SEC_VIEWED] = $src[self::METRIC_VIEW_COUNT] * self::VIEW_EVENT_INTERVAL;
	}
	
	// reports
	public static function partnerTotal($partnerId, $filter)
	{
		// view events
		$query = self::getBaseTimeseriesQuery($partnerId, $filter, null);
		self::addBaseAggregations($query);
		$item = self::runGranularityAllQuery($query);
				
		$result = array();
		self::updateBaseFields($result, $item);

		return array($result);
	}
		
	public static function entryTotal($partnerId, $filter)
	{
		// view events
		$query = self::getBaseTopNQuery(
			$partnerId, 
			$filter, 
			null, 
			self::DIMENSION_ENTRY_ID, 
			self::METRIC_VIEW_COUNT);
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
			self::METRIC_VIEW_COUNT);
		$query[self::DRUID_AGGR] = array(
			self::getViewCountAggregator(),
			self::getAudienceAggregator(),
			self::getDvrAudienceAggregator(),
		);
		
		$queryResult = self::runGranularityPeriodQuery($query, self::VIEW_EVENT_PERIOD);
		
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
					$value = intval($entryResult[$src]);
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

	public static function entrySyndicationTotal($partnerId, $filter)
	{
		$query = self::getBaseTopNQuery(
			$partnerId, 
			$filter, 
			array(self::EVENT_TYPE_PLAY), 
			self::DIMENSION_URL, 
			self::OUTPUT_PLAYS);
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
	
	public static function entryTimeline($partnerId, $filter)
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
	
	public static function entryGeoTimeline($partnerId, $filter)
	{
		// Note: while the report is called 'time line' we use granularity all since
		//		all requests have fromTime = toTime, and grouping by 'time' seems like 
		//		too much information to present.
		
		$dimensions = array(
			self::DIMENSION_ENTRY_ID => self::OUTPUT_ENTRY_ID,
			self::DIMENSION_LOCATION_CITY => self::OUTPUT_CITY_NAME,
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
			self::MAX_RESULTS, 
			array(self::getOrderByColumnSpec(
				$orderByField, 
				self::DRUID_DESCENDING, 
				self::DRUID_NUMERIC)
			));
		
		self::addBaseAggregations($query);
		
		$query[self::DRUID_GRANULARITY] = self::getGranularityAll();
		$queryResult = self::runQuery($query);
	
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
