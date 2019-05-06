<?php
/**
 * @package plugins.videoIndexerDistribution
 * @subpackage api.objects
 */
class KalturaVideoIndexerDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $ingestUrl;

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
	public $xsltFile;


	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)
	 */
	private static $map_between_objects = array
	(
		'ingestUrl',
		'username',
		'password',
		'xsltFile',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @param VideoIndexerDistributionProfile $srcObj
	 * @param KalturaDetachedResponseProfile $responseProfile
	 */
	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);
	}
}