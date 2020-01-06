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
	const DRUID_BOUND_FILTER = 'bound';
	const DRUID_AND = 'and';
	const DRUID_OR = 'or';
	const DRUID_NOT = 'not';
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
	const DRUID_DATASOURCE = 'dataSource';
	const DRUID_INTERVALS = 'intervals';
	const DRUID_FIELD = 'field';
	const DRUID_FIELDS = 'fields';
	const DRUID_CARDINALITY = 'cardinality';
	const DRUID_HYPER_UNIQUE = 'hyperUnique';
	const DRUID_HYPER_UNIQUE_CARDINALITY = 'hyperUniqueCardinality';
	const DRUID_POST_AGGR = 'postAggregations';
	const DRUID_AGGR = 'aggregations';
	const DRUID_FIELD_ACCESS = 'fieldAccess';
	const DRUID_CONSTANT = 'constant';
	const DRUID_GRANULARITY_PERIOD = 'period';
	const DRUID_TIMEZONE = 'timeZone';
	const DRUID_NUMERIC = 'numeric';
	const DRUID_INVERTED = 'inverted';
	const DRUID_CONTEXT = 'context';
	const DRUID_COMMENT = 'comment';		// Note: not really defined in druid, anything we put on the context of the query gets printed to log
	const DRUID_PRIORITY = 'priority';
	const DRUID_SKIP_EMPTY_BUCKETS = 'skipEmptyBuckets';
	const DRUID_TIMEOUT = 'timeout';
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
	const DRUID_OUTPUT_NAME = 'outputName';
	const DRUID_LIST_FILTERED = 'listFiltered';
	const DRUID_DELEGATE = 'delegate';
	const DRUID_LOWER = 'lower';
	const DRUID_UPPER = 'upper';
	const DRUID_LOWER_STRICT = 'lowerStrict';
	const DRUID_UPPER_STRICT = 'upperStrict';
	const DRUID_ORDERING = 'ordering';
	const DRUID_DOUBLE_LEAST = 'doubleLeast';
	const DRUID_EXTRACTION = 'extraction';
	const DRUID_EXTRACTION_FUNC = 'extractionFn';
	const DRUID_TIME_FORMAT = 'timeFormat';
	
	// druid response keywords
	const DRUID_TIMESTAMP = 'timestamp';
	const DRUID_EVENT = 'event';
	const DRUID_RESULT = 'result';
	const DRUID_ERROR = 'error';
	const DRUID_ERROR_CLASS = 'errorClass';
	const DRUID_ERROR_MSG = 'errorMessage';

	// kConf params
	const DRUID_URL = "druid_url";
	const DRUID_QUERY_TIMEOUT = 'druid_timeout';

	const COMMENT_MARKER = '@COMMENT@';

	protected static $curl_handle = null;
	
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

	protected static function getBoundFilter($dimension, $lower, $upper, $order, $strict = 'false')
	{
		$bound_filter = array(
			self::DRUID_TYPE => self::DRUID_BOUND_FILTER,
			self::DRUID_DIMENSION => $dimension,
		);

		if (isset($lower))
		{
			$bound_filter[self::DRUID_LOWER] = $lower;
			$bound_filter[self::DRUID_LOWER_STRICT] = $strict;
		}
		if (isset($upper))
		{
			$bound_filter[self::DRUID_UPPER] = $upper;
			$bound_filter[self::DRUID_UPPER_STRICT] = $strict;
		}
		if (isset($order))
		{
			$bound_filter[self::DRUID_ORDERING] = $order;
		}

		return $bound_filter;
	}
	
	protected static function getAndFilter($subFilters)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_AND,
			self::DRUID_FIELDS => $subFilters,
		);
	}

	protected static function getOrFilter($subFilters)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_OR,
			self::DRUID_FIELDS => $subFilters,
		);
	}

	protected static function getNotFilter($filter)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_NOT,
			self::DRUID_FIELD => $filter,
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
	
	protected static function getCardinalityAggregator($name, $fields)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_CARDINALITY,
			self::DRUID_NAME => $name,
			self::DRUID_FIELDS => $fields
		);
	}
	
	protected static function getHyperUniqueAggregator($name, $fieldName)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_HYPER_UNIQUE,
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
	
	protected static function getConstantPostAggregator($name, $value)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_CONSTANT, 
			self::DRUID_NAME => $name, 
			self::DRUID_VALUE => $value);
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

	protected static function getHyperUniqueCardinalityPostAggregator($name, $fieldName)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_HYPER_UNIQUE_CARDINALITY,
			self::DRUID_NAME => $name,
			self::DRUID_FIELD_NAME => $fieldName
		);
	}

	protected static function getDoubleLeastPostAggregator($name, $fields)
	{
		return array(
			self::DRUID_TYPE => self::DRUID_DOUBLE_LEAST,
			self::DRUID_NAME => $name,
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
	
	protected static function runQuery($content, $cache = null, $cacheExpiration = 0)
	{
		if (isset($content[self::DRUID_FILTER]) && !$content[self::DRUID_FILTER])
		{
			return array();
		}

		kApiCache::disableConditionalCache();
		
		if (!isset($content[self::DRUID_CONTEXT]))
		{
			$content[self::DRUID_CONTEXT] = array();
		}

		$content[self::DRUID_CONTEXT][self::DRUID_COMMENT] = self::COMMENT_MARKER;

		$timeout = kConf::get(self::DRUID_QUERY_TIMEOUT, 'local', null);
		if ($timeout)
		{
			$content[self::DRUID_CONTEXT][self::DRUID_TIMEOUT] = intval($timeout);
		}

		KalturaLog::log('{' . print_r($content, true) . '}');

		$post = json_encode($content);

		if ($cache)
		{
			$cacheKey = 'druidQuery-' . md5($post);
			$response = $cache->get($cacheKey);
			if ($response)
			{
				$result = json_decode($response, true);
				if ($result)
				{
					KalturaLog::log("Returning from cache $cacheKey");
					return $result;
				}
			}
		}

		$uniqueId = new UniqueId();
		$clientTag = kCurrentContext::$client_lang;
		$comment = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
		$comment .= "[$uniqueId][$clientTag]";
		$post = str_replace(json_encode(self::COMMENT_MARKER), json_encode($comment), $post);
		KalturaLog::log($post);
			
		$url = kConf::get(self::DRUID_URL);

		if (!self::$curl_handle)
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
			self::$curl_handle = $ch;
		}
		else
		{
			$ch = self::$curl_handle;
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		for ($retry = 0; $retry < 3; $retry++)
		{
			$startTime = microtime(true);
			$response = curl_exec($ch);
			$druidTook = microtime(true) - $startTime;

			KalturaLog::debug('Druid query took - ' . $druidTook. ' seconds');

			if (curl_errno($ch))
			{
				throw new Exception('Error while trying to connect to:'. $url .' error=' . curl_error($ch));
			}

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($httpCode != KCurlHeaderResponse::HTTP_STATUS_OK)
			{
				if (strpos($response, 'Query timeout') !== false)
				{
					KalturaLog::err('Druid Query timed out.');
					throw new kCoreException("Druid Query timed out", kCoreException::DRUID_QUERY_TIMED_OUT);
				}

				throw new Exception('Got invalid status code from druid: ' . $httpCode);
			}

			// Note: not closing the curl handle so that the connection can be reused

			$result = json_decode($response, true);

			KalturaMonitorClient::monitorDruidQuery(
				parse_url($url, PHP_URL_HOST),
				$content[self::DRUID_DATASOURCE],
				$content[self::DRUID_QUERY_TYPE],
				strlen($post),
				$druidTook,
				isset($result[self::DRUID_ERROR]) ? 
					$result[self::DRUID_ERROR_CLASS] . ',' . $result[self::DRUID_ERROR] : '');

			if (isset($result[self::DRUID_ERROR]) &&
				strpos($result[self::DRUID_ERROR_MSG], 'Channel disconnected') !== false)
			{
				KalturaLog::log('Retrying on error ' . $result[self::DRUID_ERROR_MSG]);
				continue;
			}

			break;
		}
	
		if (isset($result[self::DRUID_ERROR])) 
		{
			KalturaLog::err('Error while running report ' . $result[self::DRUID_ERROR_MSG]);
			throw new Exception('Error while running report ' . $result[self::DRUID_ERROR_MSG]);
		}

		if ($cache)
		{
			KalturaLog::log("Saving query response to cache $cacheKey");
			$cache->set($cacheKey, $response, $cacheExpiration);
		}

		return $result;
	}
}
