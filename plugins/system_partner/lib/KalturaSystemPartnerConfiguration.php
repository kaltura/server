<?php
class KalturaSystemPartnerConfiguration extends KalturaObject
{
	/**
	 * @var string
	 */
	public $host;
	
	/**
	 * @var string
	 */
	public $cdnHost;
	
	/**
	 * @var int
	 */
	public $maxBulkSize;
	
	/**
	 * @var int
	 */
	public $partnerPackage;
	
	/**
	 * @var bool
	 */
	public $liveStreamEnabled;
	
	/**
	 * @var bool
	 */
	public $moderateContent;
	
	/**
	 * @var string 
	 */
	public $rtmpUrl;
	
	/**
	 * @var bool
	 */
	public $storageDeleteFromKaltura;
	
	/**
	 * @var KalturaStorageServePriority
	 */
	public $storageServePriority;
	
	/**
	 * 
	 * @var int
	 */
	public $kmcVersion;
	
	/**
	 * @var bool
	 */
	public $enableAnalyticsTab;
	
	/**
	 * @var bool
	 */
	public $enableSilverLight;
	
	/**
	 * @var bool
	 */
	public $enableVast;
	
	/**
	 * @var bool
	 */
	public $enable508Players;
	
	/**
	 * @var bool
	 */
	public $enableMetadata;
	
	/**
	 * @var bool
	 */
	public $enableAuditTrail;
	
	private static $map_between_objects = array
	(
		"host",
		"cdnHost",
		"maxBulkSize",
		"partnerPackage",
		"liveStreamEnabled",
		"moderateContent",
		"rtmpUrl",
		"storageDeleteFromKaltura",
		"storageServePriority",
		"kmcVersion",
		"enableAnalyticsTab",
		"enableSilverLight",
		"enableVast",
		"enable508Players",
		"enableMetadata",
		"enableAuditTrail",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}