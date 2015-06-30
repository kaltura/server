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
	 * @var int
	 */
	public $schemaId;

	/**
	 * @var string
	 */
	public $language;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)
	 */
	private static $map_between_objects = array 
	(
		'ingestUrl',
		'username',
		'password',
		'schemaId',
		'language'
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}