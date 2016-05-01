<?php
/**
 * api for getting analytics data
 * @service analytics
 * @package api
 * @subpackage services
 */
class AnalyticsService extends KalturaBaseService
{

	const PARTNER_DIMENSION = "partner";

	/**
	 * report query action allows to get a analytics data for specific query dimensions, metrics and filters.
	 *
	 * @action query
	 * @param KalturaAnalyticsFilter $filter the analytics query filter
	 * @return KalturaReportResponse
	 */
	public function queryAction($filter)
	{

		$filter->validateForUsage($filter);
		
		$dimensionsArr = $this->extractDimensions($filter->dimensions);
		KalturaLog::debug('Extracted dimensions: ' . var_export($dimensionsArr, true));
		$metricsArr = $this->extractMetrics($filter->metrics);
		KalturaLog::debug('Extracted metrics: ' . var_export($metricsArr, true));
		$filtersArr = $this->extractFilters($filter->filters);
		KalturaLog::debug('Extracted filters: ' . var_export($filtersArr, true));

		$internalApiRequest = $this->constructInternalRequest($filter->from, $filter->to, $dimensionsArr, $metricsArr, $filtersArr, $filter->utcOffset);
		KalturaLog::info('Constructed request: ' . var_export($internalApiRequest, true));

		$internalApiServer = kConf::get('analytics_internal_API_url');
		KalturaLog::debug('Querying against: ' . var_export($internalApiServer, true));

		$apiCallResponse = $this->callAPI("POST", $internalApiServer, $internalApiRequest);
		KalturaLog::info('API call response: ' . var_export($apiCallResponse, true));

		$jsonResponse = json_decode($apiCallResponse);
		KalturaLog::debug('Response as json: ' . var_export($jsonResponse, true));

		$res = new KalturaReportResponse();
		$res->columns = implode(",", $jsonResponse->headers);
		$tempResult = array_map(array($this, 'implodeWithComma'), $jsonResponse->data);
		$res->results = array_map(array($this, 'createKalturaString'), $tempResult);

		KalturaLog::info('Response: ' . var_export($res, true));

		return $res;
	}

	private function createKalturaString($str)
	{
		$res = new KalturaString();
		$res->value = $str;
		return $res;
	}

	private function extractFilters($filters)
	{
		$res = array();
		if ($filters != null)
		{
			$res = array_map(array($this, 'extractFilter'), $filters->toArray());
		}

		// Add a filter for the current partner
		$partnerFilter = array();
		$partnerFilter['dimension'] = self::PARTNER_DIMENSION;
		$partnerFilter['values'] = array($this->getPartnerId());

		$res[] = $partnerFilter;
		return $res;
	}

	private function extractFilter($filter)
	{
		if (strtolower($filter->dimension) == self::PARTNER_DIMENSION)
		{
			throw new APIException(KalturaErrors::ANALYTICS_FORBIDDEN_FILTER);
		}

		KalturaLog::debug('Extracting filter: ' . var_export($filter, true));

		$res = array();
		$res['dimension'] = $filter->dimension;
		$res['values'] = $this->explodeAndTrim($filter->values);

		KalturaLog::debug('Extracted filter: ' . var_export($res, true));

		return $res;
	}

	private function implodeWithComma($arr)
	{
		return implode(",", $arr);
	}

	private function extractMetrics($metrics)
	{
		return $this->explodeAndTrim($metrics);
	}

	private function extractDimensions($dimensions)
	{
		if ($dimensions == null)
		{
			return array();
		}

		return $this->explodeAndTrim($dimensions);
	}

	private function explodeAndTrim($arr)
	{
		return array_map('trim', explode(",",$arr));
	}

	private function constructInternalRequest($from, $to, $dimensionsArr, $metricsArr, $filtersArr, $utcOffset)
	{
		$data = array("from" => $from, "to" => $to, "dimensions" => $dimensionsArr, "filters" => $filtersArr, "metrics" => $metricsArr, "utcOffset" => $utcOffset);
		//e.g. {"from":"1","to":"2","dimensions":["partner"], "filters":[{"dimension":"partner","values":["1"]}], "metrics":["play"], "utcOffset":"240"}
		return json_encode($data);
	}

	private function callAPI($method, $url, $data = false)
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));

		switch ($method)
		{
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);
				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);

		if (!$result)
		{
			throw new APIException(KalturaErrors::ANALYTICS_QUERY_FAILURE, curl_error($curl));
		}

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($code < 200 || $code > 300)
		{
			throw new APIException(KalturaErrors::ANALYTICS_QUERY_FAILURE, $result);
		}

		curl_close($curl);

		return $result;
	}
}