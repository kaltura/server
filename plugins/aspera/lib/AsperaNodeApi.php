<?php

/**
 * @package plugins.aspera
 * @subpackage lib
 */
class AsperaNodeApi
{
	/**
	 * @var string
	 */
	protected $_nodeUser;

	/**
	 * @var string
	 */
	protected $_nodePassword;

	/**
	 * @var string
	 */
	protected $_nodeHost;

	/**
	 * @var string
	 */
	protected $_nodePort;

	/**
	 * @var string
	 */

	public function __construct($nodeUser, $nodePassword, $nodeHost, $nodePort)
	{
		$this->_nodeUser = $nodeUser;
		$this->_nodePassword = $nodePassword;
		$this->_nodeHost = $nodeHost;
		$this->_nodePort = $nodePort;
	}

	public function getToken($path, array $extraParams = array())
	{
		$defaultParams = array(
			'transfer_requests' => array(
				'transfer_request' => array(
					'paths' => array(
						'source' => $path,
					),
					'authentication' => 'token',
				),
			),
		);

		$params = array_merge_recursive($defaultParams, $extraParams);

		return $this->callNodeApi('/files/download_setup', $params);
	}

	public function callNodeApi($service, $params)
	{
		$url = $this->getNodeApiBaseUrl($service, $params);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$responseStr = curl_exec($curl);
		KalturaLog::info('Raw response from aspera node api: '. $responseStr);
		$error = curl_error($curl);
		if ($error)
			throw new kCoreException('Failed to call node api server: ' . $error);

		$response = json_decode($responseStr);
		if (is_null($response))
		{
			// the json response from the node api could be wrapped in an extra '{' and '}', which doesn't decode well in php
			// small hack to remove it and try decoding again
			if (strlen($responseStr) > 2)
			{
				$responseStr = substr($responseStr, 1);
				$responseStr = substr($responseStr, 0, strlen($responseStr) - 1);
			}
			$response = json_decode($responseStr);
		}

		if (is_null($response))
		{
			throw new kCoreException('Aspera node api response could not be decoded');
		}

		return $response;
	}

	protected function getNodeApiBaseUrl($service)
	{
		return 'https://'.$this->_nodeUser.':'.$this->_nodePassword.'@'.$this->_nodeHost.':'.$this->_nodePort.'/'.$service;
	}
}