<?php
class KalturaYouTubeDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * @var string
	 */
	public $username;
	
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
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'username',
		'notificationEmail',
		'sftpHost',
		'sftpLogin',
		'sftpPublicKey',
		'sftpPrivateKey',
		'ownerName',
		'defaultCategory',
		'allowComments',
		'allowEmbedding',
		'allowRatings',
		'allowResponses',
		'commercialPolicy',
		'ugcPolicy',
		'target'
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}