<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage api.objects
 */
class KalturaTvinciDistributionProfile extends KalturaConfigurableDistributionProfile
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
	 * Tags array for Tvinci distribution
	 * @var KalturaTvinciDistributionTagArray
	 */
	public $tags;

	/**
	 * @var string
	 */
	public $xsltFile;

	/**
	 * @var string
	 */
	public $innerType;

	/**
	 * @var KalturaTvinciAssetsType
	 */
	public $assetsType;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)
	 */
	private static $map_between_objects = array 
	(
		'ingestUrl',
		'username',
		'password',
		'tags',
		'xsltFile',
		'innerType',
		'assetsType',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @param TvinciDistributionProfile $srcObj
	 * @param KalturaDetachedResponseProfile $responseProfile
	 */
	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);
		$this->tags = KalturaTvinciDistributionTagArray::fromDbArray($srcObj->getTags());
	}
}