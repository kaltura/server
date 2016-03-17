<?php
/**
 * @package plugins.facebookDistribution
 * @subpackage api.objects
 */
class KalturaFacebookDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $apiAuthorizeUrl;

	/**
	 *
	 * @var string
	 */
	public $pageId;

	/**
	 * @var string
	 */
	public $pageAccessToken;

	/**
	 *
	 * @var string
	 */
	public $userAccessToken;

	/**
	 *
	 * @var string
	 */
	public $state;

	/**
	 *
	 * @var string
	 */
	public $permissions;

	/**
	 *
	 * @var int
	 */
	public $reRequestPermissions;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array
	(
		'apiAuthorizeUrl',
		'pageId',
		'pageAccessToken',
		'userAccessToken',
		'state',
		'permissions',
		'reRequestPermissions'

	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}