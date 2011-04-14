<?php

/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_SessionService extends Kaltura_Client_ServiceBase
{
	function __construct(Kaltura_Client_Client $client = null)
	{
		parent::__construct($client);
	}

	function end()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("session", "end", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		return $resultObject;
	}
}
