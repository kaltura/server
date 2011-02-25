<?php
/**
 * @package Kaltura
 * @subpackage Client
 */
class Kaltura_Client_Configuration
{
	private $logger;

	public $serviceUrl    				= "http://www.kaltura.com/";
	public $partnerId    				= null;
	public $format        				= 3;
	public $clientTag 	  				= "php5";
	public $curlTimeout   				= 10;
	public $startZendDebuggerSession 	= false;
	
	/**
	 * Constructs new Kaltura configuration object
	 *
	 */
	public function __construct($partnerId = -1)
	{
	    if (!is_numeric($partnerId))
	        throw new Kaltura_Client_ClientException("Invalid partner id", Kaltura_Client_ClientException::ERROR_INVALID_PARTNER_ID);
	        
	    $this->partnerId = $partnerId;
	}
	
	/**
	 * Set logger to get kaltura client debug logs
	 *
	 * @param Kaltura_Client_ILogger $log
	 */
	public function setLogger(Kaltura_Client_ILogger $log)
	{
		$this->logger = $log;
	}
	
	/**
	 * Gets the logger (Internal client use)
	 *
	 * @return Kaltura_Client_ILogger
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}
