<?php

class kDruidBase
{
	// druid query keywords
	const DRUID_QUERY_TYPE = 'queryType';
	const DRUID_TOPN = 'topN';
	const DRUID_TIMESERIES = 'timeseries';
	const DRUID_GROUP_BY = 'groupBy';
	const DRUID_FILTERED_AGGR = 'filtered';
	const DRUID_SELECTOR_FILTER = 'selector';
	const DRUID_IN_FILTER = 'in';
	const DRUID_TYPE = 'type';
	const DRUID_FILTER = 'filter';
	const DRUID_DIMENSION = 'dimension';
	const DRUID_DIMENSIONS = 'dimensions';
	const DRUID_VALUE = 'value';
	const DRUID_VALUES = 'values';
	const DRUID_ARITHMETIC_POST_AGGR = 'arithmetic';
	const DRUID_FUNCTION = 'fn';
	const DRUID_AGGREGATOR = 'aggregator';
	const DRUID_NAME = 'name';
	const DRUID_METRIC = 'metric';
	const DRUID_THRESHOLD = 'threshold';
	const DRUID_FIELD_NAME = 'fieldName';
	const DRUID_LONG_SUM_AGGR = 'longSum';
	const DRUID_DOUBLE_SUM_AGGR = 'doubleSum';
	const DRUID_GRANULARITY = 'granularity';
	const DRUID_GRANULARITY_ALL = 'all';
	const DRUID_GRANULARITY_DAY = 'day';
	const DRUID_GRANULARITY_HOUR = 'hour';
	const DRUID_DATASOURCE = 'dataSource';
	const DRUID_INTERVALS = 'intervals';
	const DRUID_FIELDS = 'fields';
	const DRUID_CARDINALITY = 'cardinality';
	const DRUID_HYPER_UNIQUE = 'hyperUnique';
	const DRUID_POST_AGGR = 'postAggregations';
	const DRUID_AGGR = 'aggregations';
	const DRUID_FIELD_ACCESS = 'fieldAccess';
	const DRUID_CONSTANT = 'constant';
	const DRUID_GRANULARITY_PERIOD = 'period';
	const DRUID_TIMEZONE = 'timeZone';
	const DRUID_NUMERIC = 'numeric';
	const DRUID_INVERTED = 'inverted';
	const DRUID_CONTEXT = 'context';
	const DRUID_PRIORITY = 'priority';
	const DRUID_SKIP_EMPTY_BUCKETS = 'skipEmptyBuckets';
	const DRUID_AND = 'and';
	const DRUID_DIRECTION = 'direction';
	const DRUID_DIMENSION_ORDER = 'dimensionOrder';
	const DRUID_ORDER_LEX = 'lexicographic';
	const DRUID_ORDER_ALPHA_NUM = 'alphanumeric';
	const DRUID_ORDER_STRLEN = 'strlen';
	const DRUID_ORDER_NUMERIC = 'numeric';
	const DRUID_ASCENDING = 'ascending';
	const DRUID_DESCENDING = 'descending';
	const DRUID_DEFAULT = 'default';
	const DRUID_LIMIT = 'limit';
	const DRUID_COLUMNS = 'columns';
	const DRUID_LIMIT_SPEC = 'limitSpec';
	const DRUID_TRUE = 'true';
	const DRUID_SEARCH = 'search';
	const DRUID_SEARCH_DIMENSIONS = 'searchDimensions';
	const DRUID_QUERY = 'query';
	const DRUID_CONTAINS = 'contains';
	const DRUID_CASE_SENSITIVE = 'case_sensitive';
	
	// druid response keywords
	const DRUID_TIMESTAMP = 'timestamp';
	const DRUID_EVENT = 'event';
	const DRUID_RESULT = 'result';
	const DRUID_ERROR = 'error';
	const DRUID_ERROR_MSG = 'errorMessage';
	
	protected static function getIntervals($fromTime, $toTime)
	{
		$fromTime = gmdate('Y-m-d\\TH:i:s\\Z', $fromTime);
		$toTime = gmdate('Y-m-d\\TH:i:s\\Z', $toTime);
		return $fromTime . '/' . $toTime;
	}
	
	protected static function parseTimestamp($ts)
	{
		list($year, $month, $day, $hour, $min, $sec, $milli) = sscanf($ts, '%d-%d-%dT%d:%d:%d.%dZ');
		return gmmktime($hour, $min, $sec, $month, $day, $year);
	}
	
	protected static function getSelectorFilter($dimension, $value)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER,
			self::DRUID_DIMENSION => $dimension,
			self::DRUID_VALUE => $value
		);
	}
	
	protected static function getInFilter($dimension, $values)
	{
		if (count($values) == 1)
		{
			return self::getSelectorFilter($dimension, reset($values));
		}
	
		return array(
			self::DRUID_TYPE => self::DRUID_IN_FILTER,
			self::DRUID_DIMENSION => $dimension,
			self::DRUID_VALUES => $values
		);
	}
	
	protected static function getAndFilter($subFilters)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_AND,
			self::DRUID_FIELDS => $subFilters,
		);
	}
	
	protected static function getGranularityAll()
	{
		return array(
			self::DRUID_TYPE => self::DRUID_GRANULARITY_ALL
		);
	}
	
	protected static function getGranularityPeriod($period)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_GRANULARITY_PERIOD,
			self::DRUID_GRANULARITY_PERIOD => $period
		);
	}
	
	protected static function getLongSumAggregator($name, $fieldName)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_LONG_SUM_AGGR,
			self::DRUID_NAME => $name,
			self::DRUID_FIELD_NAME => $fieldName
		);
	}
	
	protected static function getDoubleSumAggregator($name, $fieldName)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_DOUBLE_SUM_AGGR,
			self::DRUID_NAME => $name,
			self::DRUID_FIELD_NAME => $fieldName
		);
	}
	
	protected static function getFilteredAggregator($filter, $aggregator)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_FILTERED_AGGR,
			self::DRUID_FILTER => $filter,
			self::DRUID_AGGREGATOR => $aggregator
		);
	}
	
	protected static function getFieldAccessPostAggregator($fieldName)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_FIELD_ACCESS,
			self::DRUID_FIELD_NAME => $fieldName
		);
	}
	
	protected static function getArithmeticPostAggregator($name, $fn, $fields)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_ARITHMETIC_POST_AGGR,
			self::DRUID_NAME => $name,
			self::DRUID_FUNCTION => $fn,
			self::DRUID_FIELDS => $fields
		);
	}
	
	protected static function getOrderByColumnSpec($dimension, $direction, $type)
	{
		return array(
			self::DRUID_DIMENSION => $dimension,
			self::DRUID_DIRECTION => $direction,
			self::DRUID_DIMENSION_ORDER => $type,
		);
	}

	protected static function getDefaultLimitSpec($limit, $orderBys)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_DEFAULT,
			self::DRUID_LIMIT => $limit,
			self::DRUID_COLUMNS => $orderBys,
		);
	}
	
	protected static function runGranularityAllQuery($query)
	{
		$query[self::DRUID_GRANULARITY] = self::getGranularityAll();
		$result = self::runQuery($query);
		if (!$result)
		{
			return array();
		}
		$result = reset($result);
		$result = $result[self::DRUID_RESULT];
		KalturaLog::log("Druid returned [" . count($result) . "] rows");
		return $result;
	}
	
	protected static function runGranularityPeriodQuery($query, $period)
	{
		$query[self::DRUID_GRANULARITY] = self::getGranularityPeriod($period);
		$result = self::runQuery($query);
		KalturaLog::log("Druid returned [" . count($result) . "] rows");
		return $result;
	}
	
	protected static function runQuery($content) 
	{
		kApiCache::disableConditionalCache();
		
		KalturaLog::log('{' . print_r($content, true) . '}');
			
		$post = json_encode($content);
		KalturaLog::log($post);
			
		$url = kConf::get('druid_url');
			
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			
		$startTime = microtime(true);
		$response = curl_exec($ch);
			
		$druidTook = microtime(true) - $startTime;
		KalturaLog::debug('Druid query took - ' . $druidTook. ' seconds');
			
		if (curl_errno($ch))
		{
			throw new Exception('Error while trying to connect to:'. $url .' error=' . curl_error($ch));
		}
			
		curl_close($ch);
			
		$result = json_decode($response, true);
	
		if (isset($result[self::DRUID_ERROR])) 
		{
			KalturaLog::err('Error while running report ' . $result[self::DRUID_ERROR_MSG]);
			throw new Exception('Error while running report ' . $result[self::DRUID_ERROR_MSG]);
		}
		return $result;
	}
}