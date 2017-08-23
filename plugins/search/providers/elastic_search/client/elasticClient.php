<?php

/**
 * @package plugins.elasticSearch
 * @subpackage client
 */
class elasticClient
{
	const ELASTIC_INDEX_KEY = "index";
	const ELASTIC_TYPE_KEY = "type";
	const ELASTIC_SIZE_KEY = "size";
	const ELASTIC_FROM_KEY = "from";
	const ELASTIC_ID_KEY = "id";
	
	protected $elasticHost;
	protected $elasticPort;
	protected $ch;
	
	/**
	 * elasticClient constructor.
	 * @param null $host
	 * @param null $port
	 * @param null $curlTimeout -timeout in seconds
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
		
		if (isset($params['body']['retry_on_conflict'])) {
			$queryParams['retry_on_conflict'] = $params['body']['retry_on_conflict'];
			unset($params['body']['retry_on_conflict']);
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
	 * @return mixed
	 */
	protected function sendRequest($cmd, $method, $body = null)
	{
		curl_setopt($this->ch, CURLOPT_URL, $cmd);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method); // PUT/GET/POST/DELETE
		if ($body)
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($body));
		
		$response = curl_exec($this->ch);
		if (!$response) {
			$code = $this->getErrorNumber();
			$message = $this->getError();
			KalturaLog::err("Elastic client curl error code[" . $code . "] message[" . $message . "]");
		} else {
			//return the response as associative array
			$response = json_decode($response, true);
			KalturaLog::debug("Elastic client response " . print_r($response, true));
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
	 * @return mixed
	 */
	public function search(array $params)
	{
		$cmd = $this->buildElasticCommandUrl($params, '', "search");
		
		if (isset($params[self::ELASTIC_SIZE_KEY]))
			$params['body'][self::ELASTIC_SIZE_KEY] = $params[self::ELASTIC_SIZE_KEY];
		
		if (isset($params[self::ELASTIC_FROM_KEY]))
			$params['body'][self::ELASTIC_FROM_KEY] = $params[self::ELASTIC_FROM_KEY];
		
		$val = $this->sendRequest($cmd, 'POST', $params['body']);
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
		
		$response = $this->sendRequest($cmd, 'PUT', $params['body']);
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
		$cmd = $this->buildElasticCommandUrl($params, $queryParams, "update");
		
		$response = $this->sendRequest($cmd, 'POST', $params['body']);
		return $response;
	}
	
	/**
	 * delete API
	 * @param array $params
	 * @return mixed
	 */
	public function delete(array $params)
	{
		$queryParams = $this->getQueryParams($params);
		$cmd = $this->buildElasticCommandUrl($params, $queryParams);
		
		$response = $this->sendRequest($cmd, 'DELETE');
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
		
		$response = $this->sendRequest($cmd, 'GET');
		return $response;
	}
	
	/**
	 * ping to check connectivity to elastic cluster
	 * @return mixed
	 */
	public function ping()
	{
		$cmd = $this->elasticHost;
		$response = $this->sendRequest($cmd, 'GET');
		return $response;
	}
	
	private function buildElasticCommandUrl(array $params, $queryParams = '', $action = null)
	{
		$cmd = $this->elasticHost;
		$cmd .= '/' . $params[self::ELASTIC_INDEX_KEY]; //index name
		
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
