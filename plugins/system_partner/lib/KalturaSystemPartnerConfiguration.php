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
	
	public function fromObject ( $source_object  )
	{
		parent::fromObject($source_object);
		
		if(class_exists('MetadataPlugin'))
			$this->enableMetadata = $source_object->getPluginEnabled(MetadataPlugin::getPluginName());
			
		if(class_exists('AuditPlugin'))
			$this->enableAuditTrail = $source_object->getPluginEnabled(AuditPlugin::getPluginName());
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		
		if(class_exists('MetadataPlugin'))
			$object_to_fill->setPluginEnabled(MetadataPlugin::getPluginName(), $this->enableMetadata);
			
		if(class_exists('AuditPlugin'))
			$object_to_fill->setPluginEnabled(AuditPlugin::getPluginName(), $this->enableAuditTrail);
			
		return $object_to_fill;
	}
}