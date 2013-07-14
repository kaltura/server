<?php
/**
 * @package plugins.verizonVcastDistribution
 * @subpackage api.objects
 */
class KalturaVerizonVcastDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $ftpHost;
	
	/**
	 * @var string
	 */
	public $ftpLogin;
	
	/**
	 * @var string
	 */
	public $ftpPass;
	
	/**
	 * @var string
	 */
	public $providerName;
	
	/**
	 * @var string
	 */
	public $providerId;
	
	/**
	 * @var string
	 */
	public $entitlement;
	
	/**
	 * @var string
	 */
	public $priority;
	
	/**
	 * @var string
	 */
	public $allowStreaming;
	
	/**
	 * @var string
	 */
	public $streamingPriceCode;
	
	/**
	 * @var string
	 */
	public $allowDownload;
	
	/**
	 * @var string
	 */
	public $downloadPriceCode;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'ftpHost',
		'ftpLogin',
		'ftpPass',
		'providerName',
		'providerId',
		'entitlement',
		'priority',
		'allowStreaming',
		'streamingPriceCode',
		'allowDownload',
		'downloadPriceCode',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}