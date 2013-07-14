<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage api.objects
 */
class KalturaFtpDistributionFile extends KalturaObject
{
	/**
	 * @var string
	 */
	public $assetId;
	
	/**
	 * @var string
	 */
	public $filename;
	
	/**
	 * @var string
	 */
	public $contents;
	
	/**
	 * @var string
	 */
	public $localFilePath;
	
	/**
	 * @var string
	 */
	public $version;
	
	/**
	 * @var string
	 */
	public $hash;
	
	/**
	 */
	public function __construct()
	{
	}
		
	/**
	 * Maps the object attributes to getters and setters for Core-to-API translation and back
	 *  
	 * @var array
	 */
	private static $map_between_objects = array
	(
		'assetId',
		'filename',
		'contents',
		'localFilePath',
		'version',
		'hash',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
