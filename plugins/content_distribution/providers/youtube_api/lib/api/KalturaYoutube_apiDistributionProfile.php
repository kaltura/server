<?php
/**
 * @package plugins.youtube_apiDistribution
 * @subpackage api.objects
 */
class KalturaYoutube_apiDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $password;
		
	/**
	 * @var string
	 */
	public $notificationEmail;
	
	/**
	 * @var string
	 */
	public $sftpHost;
	
	/**
	 * 
	 * @var string
	 */
	public $sftpLogin;
	
	/**
	 * 
	 * @var string
	 */
	public $sftpPublicKey;
	
	/**
	 * 
	 * @var string
	 */
	public $sftpPrivateKey;
	
	/**
	 * 
	 * @var string
	 */
	public $ownerName;
	
	/**
	 * 
	 * @var string
	 */
	public $defaultCategory;
		
	/**
	 * 
	 * @var string
	 */
	public $allowComments;
	
	/**
	 * 
	 * @var string
	 */
	public $allowEmbedding;
	
	/**
	 * 
	 * @var string
	 */
	public $allowRatings;
	
	/**
	 * 
	 * @var string
	 */
	public $allowResponses;
	
	/**
	 * 
	 * @var string
	 */
	public $commercialPolicy;
	
	/**
	 * 
	 * @var string
	 */
	public $ugcPolicy;
		
	/**
	 * @var string
	 */
	public $target;

	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'username',
		'password'
		'notificationEmail',
		'ownerName',
		'defaultCategory',
		'allowComments',
		'allowEmbedding',
		'allowRatings',
		'allowResponses',
		'commercialPolicy',
		'ugcPolicy',
		'target',
		'metadataProfileId',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}