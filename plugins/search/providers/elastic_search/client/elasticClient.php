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

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    const ELASTIC_ACTION_UPDATE = 'update';
    const ELASTIC_ACTION_SEARCH = 'search';
    const ELASTIC_ACTION_DELETE_BY_QUERY = 'delete_by_query';

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
        if(!$host)
            $host = kConf::get('elasticHost', 'elastic', null);
        $this->elasticHost = $host;

        if(!$port)
            $port = kConf::get('elasticPort', 'elastic', null);;
        $this->elasticPort = $port;

        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($this->ch, CURLOPT_PORT, $this->elasticPort);

        if(!$curlTimeout)
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

        if(isset($params[self::ELASTIC_BODY_KEY][self::ELASTIC_RETRY_ON_CONFLICT_KEY]))
        {
            $queryParams[self::ELASTIC_RETRY_ON_CONFLICT_KEY] = $params[self::ELASTIC_BODY_KEY][self::ELASTIC_RETRY_ON_CONFLICT_KEY];
            unset($params[self::ELASTIC_BODY_KEY][self::ELASTIC_RETRY_ON_CONFLICT_KEY]);
        }


        if(count($queryParams) >0)
        {
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
     * @throws kESearchException
     */
    protected function sendRequest($cmd, $method, $body = null)
    {
        curl_setopt($this->ch, CURLOPT_URL, $cmd);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method); // PUT/GET/POST/DELETE
        if($body)
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($body));

        $response = curl_exec($this->ch);
        if (!$response)
        {
            $code = $this->getErrorNumber();
            $message = $this->getError();
            KalturaLog::err("Elastic client curl error code[".$code."] message[".$message."]");
        }
        else
        {
            //return the response as associative array
            $response = json_decode($response, true);
            if(isset($response['error']))
            {
                $data = array();
                $data['errorMsg'] = $response['error'];
                $data['status'] = $response['status'];
                throw new kESearchException('Elastic search engine error ['.print_r($response,true).']', kESearchException::ELASTIC_SEARCH_ENGINE_ERROR, $data);
            }
            KalturaLog::debug("Elastic client response ".print_r($response,true));
        }

        return $response;
    }

    /**
     * @return bool|string
     */
    public function getError()
    {
        $err = curl_error($this->ch);
        if(!strlen($err))
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
     * @param array$params
     * @return mixed
     */
    public function search(array $params)
    {
        $cmd = $this->buildElasticCommandUrl($params, '', self::ELASTIC_ACTION_SEARCH);

        if (isset($params[self::ELASTIC_SIZE_KEY]))
            $params[self::ELASTIC_BODY_KEY][self::ELASTIC_SIZE_KEY] = $params[self::ELASTIC_SIZE_KEY];

        if (isset($params[self::ELASTIC_FROM_KEY]))
            $params[self::ELASTIC_BODY_KEY][self::ELASTIC_FROM_KEY] = $params[self::ELASTIC_FROM_KEY];

        $val = $this->sendRequest($cmd, self::POST, $params[self::ELASTIC_BODY_KEY]);
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

        $response = $this->sendRequest($cmd, self::PUT, $params[self::ELASTIC_BODY_KEY]);
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

        $response = $this->sendRequest($cmd, self::POST, $params[self::ELASTIC_BODY_KEY]);
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
        $validate  = $this->validateParamsForDelete($params);
        if(!$validate)
            throw new kESearchException('Missing mandatory params for delete in elastic client', kESearchException::MISSING_PARAMS_FOR_DELETE);

        $queryParams = $this->getQueryParams($params);
        $cmd = $this->buildElasticCommandUrl($params, $queryParams);

        $response = $this->sendRequest($cmd, self::DELETE);
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

        $response = $this->sendRequest($cmd, self::POST, $params[self::ELASTIC_BODY_KEY]);
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

        $response = $this->sendRequest($cmd, self::GET);
        return $response;
    }

    /**
     * ping to check connectivity to elastic cluster
     * @return mixed
     */
    public function ping()
    {
        $cmd = $this->elasticHost;
        $response = $this->sendRequest($cmd, self::GET);
        return $response;
    }

    /**
     * return info about the master node of the cluster
     */
    public function getMasterInfo()
    {
        $cmd = $this->elasticHost . '/_cat/master?format=json';
        $response = $this->sendRequest($cmd, self::GET);
        return $response;
    }

    /**
     * return true if index, type and document id are set
     * @param $params
     * @return bool
     */
    private function validateParamsForDelete($params)
    {
        if( isset($params[self::ELASTIC_INDEX_KEY]) && (strlen($params[self::ELASTIC_INDEX_KEY]) > 0) &&
            isset($params[self::ELASTIC_TYPE_KEY]) && (strlen($params[self::ELASTIC_TYPE_KEY]) > 0) &&
            isset($params[self::ELASTIC_ID_KEY]) && (strlen($params[self::ELASTIC_ID_KEY]) > 0) )
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
