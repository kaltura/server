<?php
/**
 * @package plugins.cortexApiDistribution
 * @subpackage api.objects
 */
class KalturaCortexApiDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $username;
	/**
	 * @var string
	 */
	public $host;
	/**
	 * @var string
	 */
	public $password;
	/**
	 * @var string
	 */
	public $folderrecordid;
	/**
	 * @var string
	 */
	public $metadataprofileid;
	/**
	 * @var string
	 */
	public $metadataprofileidpushing;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'host',
		'username',
		'password',
		'folderrecordid',
		'sourcerecordid',
		'metadataprofileid',
		'metadataprofileidpushing',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::doFromObject($srcObj, $responseProfile)
	 */
	protected function doFromObject($distributionProfile, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $distributionProfile CortexApiDistributionProfile */
		parent::doFromObject($distributionProfile, $responseProfile);
	}
}
