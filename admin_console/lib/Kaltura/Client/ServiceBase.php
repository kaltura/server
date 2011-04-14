<?php
/**
 * Abstract base class for all client services
 *  
 * @package Admin
 * @subpackage Client
 */
abstract class Kaltura_Client_ServiceBase
{
	/**
	 * @var Kaltura_Client_Client
	 */
	protected $client;
	
	/**
	 * Initialize the service keeping reference to the Kaltura_Client_Client
	 *
	 * @param Kaltura_Client_Client $client
	 */
	public function __construct(Kaltura_Client_Client $client = null)
	{
		$this->client = $client;
	}
						
	/**
	 * @param Kaltura_Client_Client $client
	 */
	public function setClient(Kaltura_Client_Client $client)
	{
		$this->client = $client;
	}
}
