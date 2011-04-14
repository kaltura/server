<?php

/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_PartnerService extends Kaltura_Client_ServiceBase
{
	function __construct(Kaltura_Client_Client $client = null)
	{
		parent::__construct($client);
	}

	function register(Kaltura_Client_Type_Partner $partner, $cmsPassword = "")
	{
		$kparams = array();
		$this->client->addParam($kparams, "partner", $partner->toParams());
		$this->client->addParam($kparams, "cmsPassword", $cmsPassword);
		$this->client->queueServiceActionCall("partner", "register", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "Kaltura_Client_Type_Partner");
		return $resultObject;
	}
}
