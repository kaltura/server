<?php
/**
 * @package plugins.freewheelGenericDistribution
 * @subpackage api.objects
 */
class KalturaFreewheelGenericDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $apikey;
	
	/**
	 * @var string
	 */
	public $email;
	
	/**
	 * @var string
	 */
	public $sftpPass;
	
	/**
	 * 
	 * @var string
	 */
	public $sftpLogin;
	
	/**
	 * 
	 * @var string
	 */
	public $contentOwner;
	
	/**
	 * 
	 * @var string
	 */
	public $upstreamVideoId;
	
	/**
	 * 
	 * @var string
	 */
	public $upstreamNetworkName;
	
	/**
	 * 
	 * @var string
	 */
	public $upstreamNetworkId;
	
	/**
	 * 
	 * @var string
	 */
	public $categoryId;
	
	/**
	 * @var bool
	 */
	public $replaceGroup;
	
	/**
	 * @var bool
	 */
	public $replaceAirDates;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'apikey',
		'email',
		'sftpPass',
		'sftpLogin',
		'contentOwner',
		'upstreamVideoId',
		'upstreamNetworkName',
		'upstreamNetworkId',
		'categoryId',
		'replaceGroup',
		'replaceAirDates',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}