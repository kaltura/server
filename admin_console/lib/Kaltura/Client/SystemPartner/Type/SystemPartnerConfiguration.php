<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSystemPartnerConfiguration';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $cdnHost = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxBulkSize = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackage = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $monitorUsage = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $liveStreamEnabled = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $moderateContent = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rtmpUrl = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $storageDeleteFromKaltura = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_StorageProfile_Enum_StorageServePriority
	 */
	public $storageServePriority = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $kmcVersion = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAnalyticsTab = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableSilverLight = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableVast = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enable508Players = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableMetadata = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableContentDistribution = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAuditTrail = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAnnotation = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableMobileFlavors = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enablePs2PermissionValidation = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $defThumbOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $adminLoginUsersQuota = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $userSessionRoleId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $adminSessionRoleId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $alwaysAllowedPermissionNames = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $importRemoteSourceForConvert = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableEntryReplacement = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableEntryReplacementApproval = null;


}

