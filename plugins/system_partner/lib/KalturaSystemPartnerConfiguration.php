<?php
class KalturaSystemPartnerConfiguration extends KalturaObject
{
	/**
	 * @var string
	 */
	public $partnerName;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $adminName;
	
	/**
	 * @var string
	 */
	public $adminEmail;
	
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
	 * @var int
	 */
	public $monitorUsage;
	
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
	public $enableContentDistribution;
	
	/**
	 * @var bool
	 */
	public $enableAuditTrail;
	
	/**
	 * @var bool
	 */
	public $enableAnnotation;
	
	/**
	 * @var int
	 */
	public $defThumbOffset;
	
	/**
	 * @var int
	 */
	public $adminLoginUsersQuota;
	
	private static $map_between_objects = array
	(
		"partnerName",
		"description",
		"adminName",
		"adminEmail",
		"host",
		"cdnHost",
		"maxBulkSize",
		"partnerPackage",
		"monitorUsage",
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
		"defThumbOffset",
		"adminLoginUsersQuota",
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
			
		if(class_exists('ContentDistributionPlugin'))
			$this->enableContentDistribution = $source_object->getPluginEnabled(ContentDistributionPlugin::getPluginName());
			
		if(class_exists('AuditPlugin'))
			$this->enableAuditTrail = $source_object->getPluginEnabled(AuditPlugin::getPluginName());
		
		if(class_exists('AnnotationPlugin'))
			$this->enableAnnotation = $source_object->getPluginEnabled(AnnotationPlugin::getPluginName());
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		
		if(class_exists('MetadataPlugin'))
			$object_to_fill->setPluginEnabled(MetadataPlugin::getPluginName(), $this->enableMetadata);
			
		if(class_exists('ContentDistributionPlugin'))
			$object_to_fill->setPluginEnabled(ContentDistributionPlugin::getPluginName(), $this->enableContentDistribution);
			
		if(class_exists('AuditPlugin'))
			$object_to_fill->setPluginEnabled(AuditPlugin::getPluginName(), $this->enableAuditTrail);
		
		if(class_exists('AnnotationPlugin'))
			$object_to_fill->setPluginEnabled(AnnotationPlugin::getPluginName(), $this->enableAnnotation);
				
		return $object_to_fill;
	}
}