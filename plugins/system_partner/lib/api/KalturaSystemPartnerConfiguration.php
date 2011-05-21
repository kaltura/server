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
	 * @var KalturaPartnerFreeTrialType
	 */
	public $monitorUsage;
	
	/**
	 * @var int
	 */
	public $freeTrialLimit;
	
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
	public $defThumbOffset;
	
	/**
	 * @var int
	 * @deprecated use limits instead
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
	 * @var int
	 */
	public $groupId;
	
	/**
	 * @var KalturaPermissionArray
	 */
	public $permissions;
	
	/**
	 * @var KalturaSystemPartnerLimitArray
	 */
	public $limits;
	
	
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
		"moderateContent",
		"rtmpUrl",
		"storageDeleteFromKaltura",
		"storageServePriority",
		"kmcVersion",
		"defThumbOffset",
		"userSessionRoleId",
		"adminSessionRoleId",
		"alwaysAllowedPermissionNames",
		"importRemoteSourceForConvert",
		"groupId" => "partnerParentId",
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
	
	public function validateForUpdate($source_object)
	{
		if(!is_null($this->limits))
		{
			foreach($this->limits as $limit)
				$limit->validate();
		}
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		
		
		if(!is_null($this->limits))
		{
			foreach($this->limits as $limit)
				$limit->apply($object_to_fill);
		}
		
		if(!is_null($this->permissions))
		{
			foreach($this->permissions as $permission)
			{
				$dbPermission = PermissionPeer::getByNameAndPartner($permission->name, $object_to_fill->getId());
				if($dbPermission)
				{
					$dbPermission->setStatus($permission->status);
				}
				else
				{
					$dbPermission = new Permission();
					$dbPermission->setType($permission->type);
					$dbPermission->setPartnerId($object_to_fill->getId());
					
					$permission->type = null;
					$dbPermission = $permission->toInsertableObject($dbPermission);
				}
				$dbPermission->save();
			}
		}
		
		return $object_to_fill;
	}
}