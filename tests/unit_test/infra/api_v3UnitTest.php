<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

/**
 * 
 * Represents the base class for api_v3 unit tests
 * @author Roni
 *
 */
class Api_v3UnitTest extends UnitTestBase
{
	/**
	 * 
	 * Gets the parameters for creating a new kaltura client and returns the new client
	 * @param int $partnerId
	 * @param string $secret
	 * @param string $configServiceUrl
	 * @return KalturaClient - a new api client 
	 */
	public function getClient($partnerId, $secret, $configServiceUrl)
	{
		$config = new KalturaConfiguration((int)$partnerId);

		//Add the server url (into the test additional data)
		$config->serviceUrl = $configServiceUrl;
		$client = new KalturaClient($config);
		$ks = $client->session->start($secret, null, KalturaSessionType::ADMIN, (int)$partnerId, null, null);
		$client->setKs($ks);

		return $client;
	}
}