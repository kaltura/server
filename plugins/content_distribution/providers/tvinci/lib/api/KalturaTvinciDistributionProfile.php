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
	 * @var string
	 */
	public $ismFileName;

	/**
	 * @var string
	 */
	public $ismPpvModule;

	/**
	 * @var string
	 */
	public $ipadnewFileName;

	/**
	 * @var string
	 */
	public $ipadnewPpvModule;

	/**
	 * @var string
	 */
	public $iphonenewFileName;

	/**
	 * @var string
	 */
	public $iphonenewPpvModule;

	/**
	 * @var string
	 */
	public $mbrFileName;

	/**
	 * @var string
	 */
	public $mbrPpvModule;

	/**
	 * @var string
	 */
	public $dashFileName;

	/**
	 * @var string
	 */
	public $dashPpvModule;

	/**
	 * @var string
	 */
	public $xsltFile;

	/**
	 * @var string
	 */
	public $widevineFileName;

	/**
	  * @var string
	  */
	public $widevinePpvModule;

	/**
	 * @var string
	 */
	public $widevineMbrFileName;

	/**
	 * @var string
	 */
	public $widevineMbrPpvModule;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)
	 */
	private static $map_between_objects = array 
	(
		'ingestUrl',
		'username',
		'password',
		'ismFileName',
		'ismPpvModule',
		'ipadnewFileName',
		'ipadnewPpvModule',
		'iphonenewFileName',
		'iphonenewPpvModule',
		'mbrFileName',
		'mbrPpvModule',
		'dashFileName',
		'dashPpvModule',
		'widevineFileName',
		'widevinePpvModule',
		'widevineMbrFileName',
		'widevineMbrPpvModule',
		'xsltFile',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}