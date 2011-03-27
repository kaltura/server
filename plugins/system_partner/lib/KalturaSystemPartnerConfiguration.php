<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
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
	 * @var bool
	 */
	public $enablePs2PermissionValidation;
	/**
	 * @var int
	 */
	public $defThumbOffset;
	
	/**
	 * @var int
	 */
	public $adminLoginUsersQuota;
	
	/**
	 * @var int
	 */
	public $userSessionRoleId;
	
	/**
	 * @var int
	 */
	public $adminSessionRoleId;
	
	/**
	 * @var string
	 */
	public $alwaysAllowedPermissionNames;
	
	/**
	 * @var bool
	 */
	public $importRemoteSourceForConvert;
	
	/**
	 * @var bool
	 */
	public $enableEntryReplacement;
	
	/**
	 * @var bool
	 */
	public $enableEntryReplacementApproval;
	
	
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
		"userSessionRoleId",
		"adminSessionRoleId",
		"alwaysAllowedPermissionNames",
		"importRemoteSourceForConvert",
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
			
		$this->enablePs2PermissionValidation = $source_object->getEnabledService(PermissionName::FEATURE_PS2_PERMISSIONS_VALIDATION);
		$this->enableEntryReplacement = $source_object->getEnabledService(PermissionName::FEATURE_ENTRY_REPLACEMENT);
		$this->enableEntryReplacementApproval = $source_object->getEnabledService(PermissionName::FEATURE_ENTRY_REPLACEMENT_APPROVAL);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		
		if(class_exists('MetadataPlugin') && !is_null($this->enableMetadata))
			$object_to_fill->setPluginEnabled(MetadataPlugin::getPluginName(), $this->enableMetadata);
			
		if(class_exists('ContentDistributionPlugin') && !is_null($this->enableContentDistribution))
			$object_to_fill->setPluginEnabled(ContentDistributionPlugin::getPluginName(), $this->enableContentDistribution);
			
		if(class_exists('AuditPlugin') && !is_null($this->enableAuditTrail))
			$object_to_fill->setPluginEnabled(AuditPlugin::getPluginName(), $this->enableAuditTrail);
		
		if(class_exists('AnnotationPlugin') && !is_null($this->enableAnnotation))
			$object_to_fill->setPluginEnabled(AnnotationPlugin::getPluginName(), $this->enableAnnotation);
				
		$object_to_fill->setEnabledService($this->enablePs2PermissionValidation, PermissionName::FEATURE_PS2_PERMISSIONS_VALIDATION);
		$object_to_fill->setEnabledService($this->enableEntryReplacement, PermissionName::FEATURE_ENTRY_REPLACEMENT);
		$object_to_fill->setEnabledService($this->enableEntryReplacementApproval, PermissionName::FEATURE_ENTRY_REPLACEMENT_APPROVAL);
		
		return $object_to_fill;
	}
}