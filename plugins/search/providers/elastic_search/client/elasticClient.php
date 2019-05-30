<?php

/**
 * @package plugins.elasticSearch
 * @subpackage client
 */
class elasticClient
{
	
	const ELASTIC_INDEX_KEY = 'index';
	const ELASTIC_TYPE_KEY = 'type';
	const ELASTIC_SIZE_KEY = 'size';
	const ELASTIC_FROM_KEY = 'from';
	const ELASTIC_ID_KEY = 'id';
	const ELASTIC_BODY_KEY = 'body';
	const ELASTIC_RETRY_ON_CONFLICT_KEY = 'retry_on_conflict';
	const ELASTIC_PREFERENCE_KEY = 'preference';
	const ELASTIC_STICKY_SESSION_PREFIX = 'ELStickySessionIndex_';

	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';

	const ELASTIC_ACTION_INDEX = 'index';
	const ELASTIC_ACTION_UPDATE = 'update';
	const ELASTIC_ACTION_SEARCH = 'search';
	const ELASTIC_ACTION_DELETE = 'delete';
	const ELASTIC_ACTION_PING = 'ping';
	const ELASTIC_GET_MASTER_INFO = 'get_master_info';
	const ELASTIC_GET_ALIAS_INFO = 'get_alias_info';
	const ELASTIC_ACTION_DELETE_BY_QUERY = 'delete_by_query';
	const ELASTIC_ACTION_GET = 'get';
	const ELASTIC_GET_INDEX = 'get_index';
	const ELASTIC_CREATE_INDEX = 'create_index';
	const ELASTIC_DELETE_INDEX = 'delete_index';
	const ELASTIC_CHANGE_ALIASES = 'change_aliases';

	const MONITOR_NO_INDEX = 'no_index';

	protected $elasticHost;
	protected $elasticPort;
	protected $ch;

	/**
	 * elasticClient constructor.
	 * @param null $host
	 * @param null $port
	 * @param null $curlTimeout - timeout in seconds
	 */
	public function __construct($host = null, $port = null, $curlTimeout = null)
	{
		if (!$host)
			$host = kConf::get('elasticHost', 'elastic', null);
		$this->elasticHost = $host;
		
		if (!$port)
			$port = kConf::get('elasticPort', 'elastic', null);;
		$this->elasticPort = $port;
		
		$this->ch = curl_init();
		
		curl_setopt($this->ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($this->ch, CURLOPT_PORT, $this->elasticPort);
		
		if (!$curlTimeout)
			$curlTimeout = kConf::get('elasticClientCurlTimeout', 'elastic', 10);
		$this->setTimeout($curlTimeout);
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	private function close()
	{
		curl_close($this->ch);
	}

	protected function getPreferenceStickySessionKey()
	{
		$ksObject = kCurrentContext::$ks_object;

		if ($ksObject && $ksObject->hasPrivilege(kSessionBase::PRIVILEGE_SESSION_KEY))
		{
			return self::ELASTIC_STICKY_SESSION_PREFIX . kCurrentContext::getCurrentPartnerId() . '_' . $ksObject->getPrivilegeValue(kSessionBase::PRIVILEGE_SESSION_KEY);
		}
		return self::ELASTIC_STICKY_SESSION_PREFIX . infraRequestUtils::getRemoteAddress();
	}

	/**
	 * @param int $seconds
	 * @return boolean
	 */
	public function setTimeout($seconds)
	{
		return curl_setopt($this->ch, CURLOPT_TIMEOUT, $seconds);
	}
	
	protected function getQueryParams(&$params)
	{
		$val = '';
		$queryParams = array();
		
		if (isset($params[self::ELASTIC_BODY_KEY][self::ELASTIC_RETRY_ON_CONFLICT_KEY]))
		{
			$queryParams[self::ELASTIC_RETRY_ON_CONFLICT_KEY] = $params[self::ELASTIC_BODY_KEY][self::ELASTIC_RETRY_ON_CONFLICT_KEY];
			unset($params[self::ELASTIC_BODY_KEY][self::ELASTIC_RETRY_ON_CONFLICT_KEY]);
		}

		if (isset($params[self::ELASTIC_PREFERENCE_KEY]))
		{
			$queryParams[self::ELASTIC_PREFERENCE_KEY] = $params[self::ELASTIC_PREFERENCE_KEY];
			unset($params[self::ELASTIC_PREFERENCE_KEY]);
		}
		
		if (count($queryParams) > 0) {
			$val .= '?';
			$val .= http_build_query($queryParams);
		}
		return $val;
	}
	
	/**
	 * send a request to elastic cluster and parse the response
	 * @param $cmd
	 * @param $method
	 * @param null $body
	 * @param $logQuery bool
	 * @param $monitorActionName string
	 * @param $monitorIndexName string
	 * @return mixed
	 * @throws kESearchException
	 */
	protected function sendRequest($cmd, $method, $body = null, $logQuery = false, $monitorActionName, $monitorIndexName)
	{
		curl_setopt($this->ch, CURLOPT_URL, $cmd);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method); // PUT/GET/POST/DELETE
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
		$jsonEncodedBody = null;
		if ($body)
		{
			$jsonEncodedBody = json_encode($body);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonEncodedBody);
			if ($logQuery)
				KalturaLog::debug("Elastic client request: ".$jsonEncodedBody);
		}

		$requestStart = microtime(true);
		$response = curl_exec($this->ch);
		$requestTook = microtime(true) - $requestStart;
		KalturaLog::debug("Elastic took - " . $requestTook . " seconds");

		KalturaMonitorClient::monitorElasticAccess($monitorActionName, $monitorIndexName, $jsonEncodedBody, $requestTook, $this->elasticHost);

		if (!$response)
		{
			$code = $this->getErrorNumber();
			$message = $this->getError();
			KalturaLog::err("Elastic client curl error code[" . $code . "] message[" . $message . "]");
		}
		else
		{
			KalturaLog::debug("Elastic client response " .$response);
			//return the response as associative array
			$response = json_decode($response, true);
			if (isset($response['error']))
			{
				$data = array();
				$data['errorMsg'] = $response['error'];
				$data['status'] = $response['status'];
				throw new kESearchException('Elastic search engine error [' . print_r($response, true) . ']', kESearchException::ELASTIC_SEARCH_ENGINE_ERROR, $data);
			}
		}
		
		return $response;
	}
	
	/**
	 * @return bool|string
	 */
	public function getError()
	{
		$err = curl_error($this->ch);
		if (!strlen($err))
			return false;
		
		return $err;
	}
	
	/**
	 * @return int
	 */
	public function getErrorNumber()
	{
		return curl_errno($this->ch);
	}
	
	/**
	 * search API
	 * @param array $params
	 * @param $logQuery bool
	 * @param $shouldAddPreference bool
	 * @return mixed
	 */
	public function search(array $params, $logQuery = false, $shouldAddPreference = false)
	{
		kApiCache::disableConditionalCache();
		if ($shouldAddPreference)
		{
			//add preference so that requests from the same session will hit the same shards
			$params[self::ELASTIC_PREFERENCE_KEY] = $this->getPreferenceStickySessionKey();
		}
		$queryParams = $this->getQueryParams($params);
		$cmd = $this->buildElasticCommandUrl($params, $queryParams, self::ELASTIC_ACTION_SEARCH);
		
		if (isset($params[self::ELASTIC_SIZE_KEY]))
			$params[self::ELASTIC_BODY_KEY][self::ELASTIC_SIZE_KEY] = $params[self::ELASTIC_SIZE_KEY];
		
		if (isset($params[self::ELASTIC_FROM_KEY]))
			$params[self::ELASTIC_BODY_KEY][self::ELASTIC_FROM_KEY] = $params[self::ELASTIC_FROM_KEY];

		$monitorIndexName = isset($params[self::ELASTIC_INDEX_KEY]) ? $params[self::ELASTIC_INDEX_KEY] : self::MONITOR_NO_INDEX;
		$val = $this->sendRequest($cmd, self::POST, $params[self::ELASTIC_BODY_KEY], $logQuery, self::ELASTIC_ACTION_SEARCH, $monitorIndexName);
		return $val;
	}
	
	/**
	 * index API
	 * @param array $params
	 * @return mixed
	 */
	public function index(array $params)
	{
		$queryParams = $this->getQueryParams($params);
		$cmd = $this->buildElasticCommandUrl($params, $queryParams);

		$monitorIndexName = isset($params[self::ELASTIC_INDEX_KEY]) ? $params[self::ELASTIC_INDEX_KEY] : self::MONITOR_NO_INDEX;
		$method = isset($params[self::ELASTIC_ID_KEY]) ? self::PUT : self::POST;//use elastic auto id creation
		$response = $this->sendRequest($cmd, $method, $params[self::ELASTIC_BODY_KEY], false, self::ELASTIC_ACTION_INDEX, $monitorIndexName);
		return $response;
	}
	
	/**
	 * update API
	 * @param array $params
	 * @return mixed
	 */
	public function update(array $params)
	{
		$queryParams = $this->getQueryParams($params);
		$cmd = $this->buildElasticCommandUrl($params, $queryParams, self::ELASTIC_ACTION_UPDATE);

		$monitorIndexName = isset($params[self::ELASTIC_INDEX_KEY]) ? $params[self::ELASTIC_INDEX_KEY] : self::MONITOR_NO_INDEX;
		$response = $this->sendRequest($cmd, self::POST, $params[self::ELASTIC_BODY_KEY], false, self::ELASTIC_ACTION_UPDATE, $monitorIndexName);
		return $response;
	}
	
	/**
	 * delete API
	 * @param array $params
	 * @return mixed
	 * @throws kESearchException
	 */
	public function delete(array $params)
	{
		$validate = $this->validateParamsForDelete($params);
		if (!$validate)
			throw new kESearchException('Missing mandatory params for delete in elastic client', kESearchException::MISSING_PARAMS_FOR_DELETE);
		
		$queryParams = $this->getQueryParams($params);
		$cmd = $this->buildElasticCommandUrl($params, $queryParams);

		$monitorIndexName = isset($params[self::ELASTIC_INDEX_KEY]) ? $params[self::ELASTIC_INDEX_KEY] : self::MONITOR_NO_INDEX;
		$response = $this->sendRequest($cmd, self::DELETE, null, false, self::ELASTIC_ACTION_DELETE, $monitorIndexName);
		return $response;
	}
	
	/**
	 * delete by query API
	 * @param array $params
	 * @return mixed
	 */
	public function deleteByQuery(array $params)
	{
		$queryParams = $this->getQueryParams($params);
		$cmd = $this->buildElasticCommandUrl($params, $queryParams, self::ELASTIC_ACTION_DELETE_BY_QUERY);

		$monitorIndexName = isset($params[self::ELASTIC_INDEX_KEY]) ? $params[self::ELASTIC_INDEX_KEY] : self::MONITOR_NO_INDEX;
		$response = $this->sendRequest($cmd, self::POST, $params[self::ELASTIC_BODY_KEY], true, self::ELASTIC_ACTION_DELETE_BY_QUERY, $monitorIndexName);
		return $response;
	}
	
	/**
	 * get API
	 * @param array $params
	 * @return mixed
	 */
	public function get(array $params)
	{
		$queryParams = $this->getQueryParams($params);
		$cmd = $this->buildElasticCommandUrl($params, $queryParams);

		$monitorIndexName = isset($params[self::ELASTIC_INDEX_KEY]) ? $params[self::ELASTIC_INDEX_KEY] : self::MONITOR_NO_INDEX;
		$response = $this->sendRequest($cmd, self::GET, null, false, self::ELASTIC_ACTION_GET, $monitorIndexName);
		return $response;
	}
	
	/**
	 * ping to check connectivity to elastic cluster
	 * @return mixed
	 */
	public function ping()
	{
		$cmd = $this->elasticHost;
		$response = $this->sendRequest($cmd, self::GET, null, false, self::ELASTIC_ACTION_PING, self::MONITOR_NO_INDEX);
		return $response;
	}
	
	/**
	 * return info about the master node of the cluster
	 */
	public function getMasterInfo()
	{
		$cmd = $this->elasticHost . '/_cat/master?format=json';
		$response = $this->sendRequest($cmd, self::GET, null, false, self::ELASTIC_GET_MASTER_INFO, self::MONITOR_NO_INDEX);
		return $response;
	}

	/**
	 * return the aliases for index name
	 * @param $indexName
	 * @return mixed
	 * @throws kESearchException
	 */
	public function getAliasesForIndicesByIndexName($indexName)
	{
		$cmd = $this->elasticHost . "/$indexName/_alias/*";
		$response = $this->sendRequest($cmd, self::GET, null, false, self::ELASTIC_GET_ALIAS_INFO, self::MONITOR_NO_INDEX);
		return $response;
	}

	/**
	 * return the index info for given index name
	 * @param $indexName
	 * @return mixed
	 * @throws kESearchException
	 */
	public function getIndexInfo($indexName)
	{
		$cmd = $this->elasticHost . "/$indexName/";
		$response = $this->sendRequest($cmd, self::GET, null, false, self::ELASTIC_GET_INDEX, self::MONITOR_NO_INDEX);
		return $response;
	}

	/**
	 * creates a new index
	 * @param $indexName
	 * @param $body - index mapping in json format
	 * @return mixed
	 * @throws kESearchException
	 */
	public function createIndex($indexName, $body)
	{
		$cmd = $this->elasticHost . "/$indexName";
		$response = $this->sendRequest($cmd, self::PUT, $body, false, self::ELASTIC_CREATE_INDEX, $indexName);
		return $response;
	}

	/**
	 * delete the index
	 * @param $indexName
	 * @return mixed
	 * @throws kESearchException
	 */
	public function deleteIndex($indexName)
	{
		$cmd = $this->elasticHost . "/$indexName";
		$response = $this->sendRequest($cmd, self::DELETE, null,false, self::ELASTIC_DELETE_INDEX, $indexName);
		return $response;
	}

	/**
	 * removes/add aliases from indices
	 * @param $body
	 * @return mixed
	 * @throws kESearchException
	 */
	public function changeAliases($body)
	{
		$cmd = $this->elasticHost . '/_aliases';
		$response = $this->sendRequest($cmd, self::POST, $body, false, self::ELASTIC_CHANGE_ALIASES, self::MONITOR_NO_INDEX);
		return $response;
	}

	/**
	 * return true if index, type and document id are set
	 * @param $params
	 * @return bool
	 */
	private function validateParamsForDelete($params)
	{
		if (isset($params[self::ELASTIC_INDEX_KEY]) && (strlen($params[self::ELASTIC_INDEX_KEY]) > 0) &&
			isset($params[self::ELASTIC_TYPE_KEY]) && (strlen($params[self::ELASTIC_TYPE_KEY]) > 0) &&
			isset($params[self::ELASTIC_ID_KEY]) && (strlen($params[self::ELASTIC_ID_KEY]) > 0)
		)
			return true;
		
		return false;
	}
	
	private function buildElasticCommandUrl(array $params, $queryParams = '', $action = null)
	{
		$cmd = $this->elasticHost;
		$cmd .= '/' . $params[self::ELASTIC_INDEX_KEY];

		if (isset($params[self::ELASTIC_TYPE_KEY]))
			$cmd .= '/' . $params[self::ELASTIC_TYPE_KEY];
		
		if (isset($params[self::ELASTIC_ID_KEY]))
			$cmd .= '/' . $params[self::ELASTIC_ID_KEY];
		
		if ($action)
			$cmd .= "/_$action";
		
		if ($queryParams != '')
			$cmd .= $queryParams;
		
		return $cmd;
	}
}
