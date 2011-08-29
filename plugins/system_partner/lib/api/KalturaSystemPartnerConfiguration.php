<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerConfiguration extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $id;
	
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
	public $partnerPackage;
	
	/**
	 * @var int
	 */
	public $monitorUsage;
	
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
	 * @var int
	 */
	public $restrictThumbnailByKs;
		
	/**
	 * @var int
	 */
	public $defThumbOffset;
	
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
	 * @var KalturaPermissionArray
	 */
	public $permissions;
	
	/**
	 * @var string
	 */
	public $notificationsConfig;
	
	/**
	 * @var bool
	 */
	public $allowMultiNotification;
		
	/**
	 * @var int
	 */
	public $loginBlockPeriod; 
	
	/**
	 * @var int
	 */
	public $numPrevPassToKeep;
	
	/**
	 * @var int
	 */
	public $passReplaceFreq;
	
	/**
	 * @var bool
	 */
	public $isFirstLogin;
	
	/**
	 * @var KalturaPartnerGroupType
	 */
	public $partnerGroupType;
	
	/**
	 * @var int
	 */
	public $partnerParentId;
	
	/**
	 * @var KalturaSystemPartnerLimitArray
	 */
	public $limits;
	
	/**
	 * http/rtmp/hdnetwork
	 * @var string
	 */
	public $streamerType;
	
	/**
	 * http/https, rtmp/rtmpe
	 * @var string
	 */
	public $mediaProtocol;
	
	/**
	 * @var string 
	 */
	public $extendedFreeTrailExpiryReason;
	
	/**
	 *  Unix timestamp (In seconds)
	 * 
	 * @var int
	 * 
	 */
	public $extendedFreeTrailExpiryDate;
	
	/**
	 * @var int
	 */
	public $extendedFreeTrail;
	
	/**
	 * @var string
	 */
	public $crmId;
	
	/**
	 * @var string
	 */
	public $crmLink;
	
	
	
	private static $map_between_objects = array
	(
		"id",
		"partnerName",
		"description",
		"adminName",
		"adminEmail",
		"host",
		"cdnHost",
		//"maxBulkSize",
		"partnerPackage",
		"monitorUsage",
		"moderateContent",
		"rtmpUrl",
		"storageDeleteFromKaltura",
		"storageServePriority",
		"kmcVersion",
		"defThumbOffset",
		//"adminLoginUsersQuota",
		"userSessionRoleId",
		"adminSessionRoleId",
		"alwaysAllowedPermissionNames",
		"importRemoteSourceForConvert",
		"notificationsConfig",
		"allowMultiNotification",
		//"maxLoginAttempts",
		"loginBlockPeriod",
		"numPrevPassToKeep",
		"passReplaceFreq",
		"isFirstLogin",
		"partnerGroupType",
		"partnerParentId",
		"streamerType",
		"mediaProtocol",
		"extendedFreeTrailExpiryDate",
		"extendedFreeTrailExpiryReason",
		"extendedFreeTrail",
		"restrictThumbnailByKs",
		"crmLink",
		"crmId"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function fromObject ( $source_object  )
	{
		parent::fromObject($source_object);
		
		$permissions = PermissionPeer::retrievePartnerLevelPermissions($source_object->getId());
		$this->permissions = KalturaPermissionArray::fromDbArray($permissions);
		$this->limits = KalturaSystemPartnerLimitArray::fromPartner($source_object);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		if (!$object_to_fill) {
			KalturaLog::err('Cannot find object to fill');
			return null;
		}
		
		if(!is_null($this->permissions))
		{
			foreach($this->permissions as $permission)
			{
				KalturaLog::debug("partner: " . $object_to_fill->getId() . " add permissions: " . print_r($permission,true));
				
				$dbPermission = PermissionPeer::getByNameAndPartner($permission->name, array($object_to_fill->getId()));
				if($dbPermission)
				{
					KalturaLog::debug("add permissions: exists; set status; " . $permission->status);
					KalturaLog::debug("db permissions:  " . print_r($dbPermission,true));
					$dbPermission->setStatus($permission->status);
				}
				else
				{
					KalturaLog::debug("add permissions: didn't exists");
					$dbPermission = new Permission();
					$dbPermission->setType($permission->type);
					$dbPermission->setPartnerId($object_to_fill->getId());
					//$dbPermission->setStatus($permission->status);
					$permission->type = null;
					$dbPermission = $permission->toInsertableObject($dbPermission);
				}
				
				KalturaLog::debug("add permissions: save" . print_r($dbPermission,true));
				$dbPermission->save();
			}
		}
		
		if (!is_null($this->limits))
		{
			foreach ($this->limits as $limit)
			{
				$limit->apply($object_to_fill);
			}
		}
		
		return $object_to_fill;
	}
}