<?php
/**
 * @package plugins.yahooDistribution
 * @subpackage api.objects
 */
class KalturaYahooDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $ftpPath;
	
	/**
	 * @var string
	 */
	public $ftpUsername;
	
	/**
	 * 
	 * @var string
	 */
	public $ftpPassword;
	
	/**
	 * @var string
	 */
	public $ftpHost;
	
	/**
	 * @var string
	 */
	public $contactTelephone;
	
	/**
	 * @var string
	 */
	public $contactEmail;	
	
	/** 
	 * @var KalturaYahooDistributionProcessFeedActionStatus
	 */
	public $processFeed;

	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'ftpPath',
		'ftpUsername',
		'ftpPassword',
		'ftpHost',
		'contactTelephone',
		'contactEmail',
		'processFeed' => 'processFeedActionStatus',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}