<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaUserEntry extends KalturaObject implements IRelatedFilterable
{

	/**
	 * unique auto-generated identifier
	 * @var int
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $id;

	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in,notin
	 */
	public $entryId;

	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in,notin
	 */
	public $userId;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var KalturaUserEntryStatus
	 * @readonly
	 * @filter eq
	 */
	public $status;

	/**
	 * @var time
	 * @readonly
	 * @filter lte,gte,order
	 */
	public $createdAt;

	/**
	 * @var time
	 * @readonly
	 * @filter lte,gte,order
	 */
	public $updatedAt;

	/**
	 * @var KalturaUserEntryType
	 * @readonly
	 * @filter eq
	 */
	public $type;
	
	/**
	 * @var KalturaUserEntryExtendedStatus
	 * @filter eq,in,notin
	 */
	public $extendedStatus;

	private static $map_between_objects = array
	(
		"id",
		"entryId",
		"userId" => "KuserId",
		"partnerId",
		"type",
		"status",
		"createdAt",
		"updatedAt",
		"type",
		"extendedStatus",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}


	/**
	 * Function returns KalturaUserEntry sub-type according to protocol
	 * @var string $type
	 * @return KalturaUserEntry
	 *
	 */
	public static function getInstanceByType ($type)
	{
		$obj = KalturaPluginManager::loadObject("KalturaUserEntry",$type);
		if (is_null($obj))
		{
			KalturaLog::err("The type '$type' is unknown");
		}
		return $obj;
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		$this->validatePropertyNotNull("entryId");
		$this->validateEntryId();
		$this->validateUserID();
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if($this->entryId !== null)
			$this->validateEntryId($sourceObject->getId());
		
		$this->validateUserID($sourceObject->getId());
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toInsertableObject($object_to_fill, $props_to_skip);
		if (empty($this->userId))
		{
			$currentKsKuser = kCurrentContext::getCurrentKsKuserId();
			$object_to_fill->setKuserId($currentKsKuser);
		}
		else
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->userId);
			if (!$kuser)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $this->userId);
			}
			$object_to_fill->setKuserId($kuser->getKuserId());
		}
		$object_to_fill->setPartnerId(kCurrentContext::getCurrentPartnerId());
		return $object_to_fill;
	}

	/**
	 * Should return the extra filters that are using more than one field
	 * On inherited classes, do not merge the array with the parent class
	 *
	 * @return array
	 */
	function getExtraFilters()
	{
		return array();
	}

	/**
	 * Should return the filter documentation texts
	 *
	 */
	function getFilterDocs()
	{
		return array();
	}

	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$kuser = $srcObj->getkuser();
		if ($kuser)
		{
			$this->userId = $kuser->getPuserId();
		}
		parent::doFromObject($srcObj, $responseProfile);
	}
	
	/*
	 * @param string $userEntryID
	 * @throw KalturaAPIException
	 */
	private function validateEntryId($userEntryID = null)
	{
		if($userEntryID === null)
		{
			$dbEntry = entryPeer::retrieveByPK($this->entryId);
			if (!$dbEntry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
		}
		else 
		{
			// update
			$dbUserEntry = UserEntryPeer::retrieveByPK($userEntryID);
			if(!$dbUserEntry)
				throw new KalturaAPIException(KalturaErrors::USER_ENTRY_NOT_FOUND, $userEntryID);
			
			if($this->entryId !== null && $this->entryId != $dbUserEntry->getEntryId())
				throw new KalturaAPIException(KalturaErrors::USER_ENTRY_DOES_NOT_MATCH_ENTRY_ID, $userEntryID);
		}
	}

	/*
	 * @param string $userEntryID
	 * @throw KalturaAPIException
	 */
	private function validateUserId($userEntryID = null)
	{
		if($userEntryID === null)
		{
			$userId = $this->userId ? $this->userId : kCurrentContext::getCurrentKsKuserId();
			if(!$userId)
				throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_PROVIDED);
		}
		else 
		{
			// update
			$dbUserEntry = UserEntryPeer::retrieveByPK($userEntryID);
			if(!$dbUserEntry)
				throw new KalturaAPIException(KalturaErrors::USER_ENTRY_NOT_FOUND, $userEntryID);
			
			if($userId != $dbUserEntry->getKuserId())
				throw new KalturaAPIException(KalturaErrors::USER_ENTRY_DOES_NOT_MATCH_USER_ID, $userEntryID);
		}
	}
	
	public function validateAlreadyExistsByType($type = null)
	{
		$userId = $this->userId ? $this->userId : kCurrentContext::getCurrentKsKuserId();
		if(!$userId || !$this->entryId)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 
					$this->getFormattedPropertyNameWithClassName('userId') . '/' . $this->getFormattedPropertyNameWithClassName('entryId'));
		
		$userEntryCriteria = new Criteria();
		
		$userEntryCriteria->add(UserEntryPeer::ENTRY_ID, $this->entryId);
		$userEntryCriteria->add(UserEntryPeer::KUSER_ID, $this->userId);
		$userEntryCriteria->add(UserEntryPeer::TYPE, $type);
		
		return UserEntryPeer::doSelectOne($userEntryCriteria);
	}
}