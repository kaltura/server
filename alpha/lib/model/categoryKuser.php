<?php


/**
 * Skeleton subclass for representing a row from the 'category_kuser' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class categoryKuser extends BasecategoryKuser implements IIndexable{
	
	private $old_status = null;
	
	const BULK_UPLOAD_ID = "bulk_upload_id";
	
	const PARTNER_INDEX_PREFIX = 'p';
	
	const UPDATE_METHOD_INDEX_PREFIX = 'um';
	
	const STATUS_INDEX_PREFIX = 'st';
	
	const PERMISSION_NAME_INDEX_PREFIX = "pn";
	
	const PERMISSION_NAME_FIELD_INDEX_PREFIX = "per";
	
	private static $indexFieldTypes = null;
		
	private static $indexFieldsMap = null;
	
	const STATUS_FIELD_PREFIX = "status";
	
	
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setUpdateMethod(UpdateMethodType::MANUAL);
	}
	
	public function setPuserId($puserId)
	{
		if ( self::getPuserId() == $puserId )  // same value - don't set for nothing 
			return;

		parent::setPuserId($puserId);
		
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
		if (!$kuser)
		    throw new kCoreException("Invalid user Id [{$puserId}]", kCoreException::INVALID_USER_ID );
			
		parent::setKuserId($kuser->getId());
		parent::setScreenName($kuser->getScreenName());
	}
	
	/**
	 * @param string $permissionName
	 * @return boolean
	 */
	public function hasPermission($permissionName)
	{
		$permissions = explode(',', $this->getPermissionNames());
		return in_array($permissionName, $permissions);
	}
	
	/* (non-PHPdoc)
	 * @see BasecategoryKuser::setKuserId()
	 */
	public function setKuserId($kuserId)
	{
		if ( self::getKuserId() == $kuserId )  // same value - don't set for nothing 
			return;

		parent::setKuserId($kuserId);

		$kuser = kuserPeer::retrieveByPK($kuserId);
		if (!$kuser)
			throw new kCoreException("Invalid kuser Id [$kuserId]", kCoreException::INVALID_USER_ID);

		parent::setPuserId($kuser->getPuserId());
		parent::setScreenName($kuser->getScreenName());
	}
	
	/* (non-PHPdoc)
	 * @see BasecategoryKuser::setStatus()
	 */
	public function setStatus($v)
	{
		$this->old_status = $this->getStatus();

		parent::setStatus($v);
	}
	
	
	/* (non-PHPdoc)
	 * @see BasecategoryKuser::preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		$this->updateCategroy();
		
		return parent::preUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseObject::preDelete()
	 */
	public function preDelete(PropelPDO $con = null)
	{
		$this->updateCategroy(true);
		
		return parent::preDelete();	
	}	
	
	
	/* (non-PHPdoc)
	 * @see BasecategoryKuser::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->updateCategroy();
		
		return parent::preInsert($con);
	}
	
	private function updateCategroy($isDelete = false)
	{
		categoryPeer::setUseCriteriaFilter(false);
		$category = categoryPeer::retrieveByPK($this->category_id);
		categoryPeer::setUseCriteriaFilter(true);
		
		if(!$category)
			throw new kCoreException('category not found');
			
		if ($this->isNew())
		{
			if($this->status == CategoryKuserStatus::PENDING)
				$category->setPendingMembersCount($category->getPendingMembersCount() + 1);
			
			if($this->status == CategoryKuserStatus::ACTIVE)
				$category->setMembersCount($category->getMembersCount() + 1);
				
			$category->save();
		}
		elseif($this->isColumnModified(categoryKuserPeer::STATUS))
		{
			if($this->status == CategoryKuserStatus::PENDING)
				$category->setPendingMembersCount($category->getPendingMembersCount() + 1);
			
			if($this->status == CategoryKuserStatus::ACTIVE )
				$category->setMembersCount($category->getMembersCount() + 1);
			
			if($this->old_status == CategoryKuserStatus::PENDING)
				$category->setPendingMembersCount($category->getPendingMembersCount() - 1);
			
			if($this->old_status == CategoryKuserStatus::ACTIVE)
				$category->setMembersCount($category->getMembersCount() - 1);
				
			$category->save();
		}
		
		if($isDelete)
		{				
			if($this->status == CategoryKuserStatus::PENDING)
				$category->setPendingMembersCount($category->getPendingMembersCount() - 1);
				
			if($this->status == CategoryKuserStatus::ACTIVE)
				$category->setMembersCount($category->getMembersCount() - 1);
				
			$category->save();
		}
		
		$this->addIndexCategoryInheritedTreeJob($category->getFullIds());
		$category->indexToSearchIndex();
	}
	
	public function addIndexCategoryInheritedTreeJob($fullIdsStartsWithCategoryId)
	{
		$featureStatusToRemoveIndex = new kFeatureStatus();
		$featureStatusToRemoveIndex->setType(IndexObjectType::CATEGORY);
		
		$featureStatusesToRemove = array();
		$featureStatusesToRemove[] = $featureStatusToRemoveIndex;

		$filter = new categoryFilter();
		$filter->setFullIdsStartsWith($fullIdsStartsWithCategoryId);
		$filter->setInheritanceTypeEqual(InheritanceType::INHERIT);
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);		
		$filter->attachToCriteria($c);		
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$categories = categoryPeer::doSelect($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		if(count($categories))
			kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::CATEGORY, $filter, true, $featureStatusesToRemove);
	}
	
	public function reSetCategoryFullIds()
	{
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		if(!$category)
			throw new kCoreException('category id [' . $this->getCategoryId() . 'was not found', kCoreException::ID_NOT_FOUND);
			
		$this->setCategoryFullIds($category->getFullIds());
	}
	
	public function reSetScreenName()
	{
		$kuser = kuserPeer::retrieveByPK($this->getKuserId());
		
		if($kuser)
		{
			$this->setScreenName($kuser->getScreenName());
		}
	}
	
	//	set properties in custom data
	
    public function setBulkUploadId ($bulkUploadId){$this->putInCustomData (self::BULK_UPLOAD_ID, $bulkUploadId);}
	public function getBulkUploadId (){return $this->getFromCustomData(self::BULK_UPLOAD_ID);}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIntId()
	 */
	public function getIntId() {
		return $this->getId();		
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getEntryId()
	 */
	public function getEntryId() {}

	/* (non-PHPdoc)
	 * @see IIndexable::getObjectIndexName()
	 */
	public function getObjectIndexName() {
		return categoryKuserPeer::TABLE_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIndexNullableFields()
	 */
	public static function getIndexNullableFields()
	{
		return array();
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap() 
	{
		if (! self::$indexFieldsMap)
		{
			self::$indexFieldsMap = array (
			'category_id' => 'categoryId',
			'kuser_id' => 'kuserId',
			'category_full_ids' => 'searchIndexCategoryFullIds',
			'permission_names' => 'searchIndexPermissionNames',
			'puser_id' => 'puserId',
			'screen_name' => 'screenName',
			'category_kuser_status' => 'searchIndexStatus',
			'partner_id' => 'partnerId',
			'update_method' => 'searchIndexUpdateMethod',
			'created_at' => 'createdAt',
			'updated_at' => 'updatedAt',
			);
		}
		
		return self::$indexFieldsMap;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldType()
	 */
	public function getIndexFieldType($field) 
	{
		if (! self::$indexFieldTypes)
		{
			self::$indexFieldTypes = array (
			'category_id' => IIndexable::FIELD_TYPE_STRING,
			'kuser_id' => IIndexable::FIELD_TYPE_STRING,
			'category_full_ids' => IIndexable::FIELD_TYPE_STRING,
			'permission_names' => IIndexable::FIELD_TYPE_STRING,
			'puser_id' => IIndexable::FIELD_TYPE_STRING,
			'screen_name' => IIndexable::FIELD_TYPE_STRING,
			'category_kuser_status' => IIndexable::FIELD_TYPE_STRING,
			'partner_id' => IIndexable::FIELD_TYPE_STRING,
			'update_method' => IIndexable::FIELD_TYPE_STRING,
			'created_at' => IIndexable::FIELD_TYPE_DATETIME,
			'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
			);
		}	
		if(isset(self::$indexFieldTypes[$field]))
			return self::$indexFieldTypes[$field];
			
		return null;
	}

	public static $sphinxFieldsEscapeType = array(
		'category_full_ids' => SearchIndexFieldEscapeType::NO_ESCAPE,
	);
	
	/* (non-PHPdoc)
	 * @see IIndexable::getSearchIndexFieldsEscapeType()
	 */
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		if(!isset(self::$sphinxFieldsEscapeType[$fieldName]))
			return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
			
		return self::$sphinxFieldsEscapeType[$fieldName];
	}

	/* (non-PHPdoc)
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex() {
		
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
	
	/**
	 * Return permission_names property value for index
	 * @return string
	 */
	public function getSearchIndexPermissionNames ()
	{
		$permissionNames = explode(",", $this->getPermissionNames());
		foreach ($permissionNames as &$permissionName)
			$permissionName = self::getSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionName, $this->getPartnerId());
		
		return self::PERMISSION_NAME_FIELD_INDEX_PREFIX.$this->getPartnerId()." ". implode(" ", $permissionNames);
	}
	
	/**
	 * Return status property value for index
	 * @return string
	 */
	public function getSearchIndexStatus ()
	{
		return self::STATUS_FIELD_PREFIX ." ". self::getSearchIndexFieldValue(categoryKuserPeer::STATUS, $this->getStatus(), $this->getPartnerId());
	}
	
	/**
	 * Return update_method property value for index
	 * @return string
	 */
	public function getSearchIndexUpdateMethod ()
	{
		return self::getSearchIndexFieldValue(categoryKuserPeer::UPDATE_METHOD, $this->getUpdateMethod(), $this->getPartnerId());
	}
	
	/**
	 * Return category_full_ids property value for index
	 * @return string
	 */
	public function getSearchIndexCategoryFullIds ()
	{
		$fullIds = $this->getCategoryFullIds();
		$fullIdsArr = explode(categoryPeer::CATEGORY_SEPARATOR, $fullIds);
		
		$parsedFullId = '';
		$fullIds = '';
		foreach ($fullIdsArr as $categoryId)
		{
			if($fullIds == '')
			{
				$fullIds = $categoryId;
			}
			else
			{
				$parsedFullId .= md5($fullIds . categoryPeer::CATEGORY_SEPARATOR) . ' ';
				$fullIds .= '>' . $categoryId;
			}
			
			$parsedFullId .= md5($fullIds) . ' ';
		}
		
		$parsedFullId .= md5($fullIds . category::FULL_IDS_EQUAL_MATCH_STRING);
		
		return $parsedFullId ;
	}
	
	public static function getSearchIndexFieldValue ($fieldName, $fieldValue, $partnerId)
	{
		switch ($fieldName)
		{
			case categoryKuserPeer::STATUS:
				return $partnerId . self::STATUS_INDEX_PREFIX . $fieldValue;
				break;
			case categoryKuserPeer::UPDATE_METHOD:
				return $partnerId . self::UPDATE_METHOD_INDEX_PREFIX . $fieldValue;
				break;
			case categoryKuserPeer::PERMISSION_NAMES:
				return $partnerId . self::PERMISSION_NAME_INDEX_PREFIX . $fieldValue;
				break;
			default:
				return $fieldValue;
			
		}
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Baseentry#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
	
		if (!$this->alreadyInSave)
		{
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
			
			$category = $this->getcategory();
			if($category && $category->getPrivacyContexts() && !PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT_USED, $category->getPartnerId()))
				PermissionPeer::enableForPartner(PermissionName::FEATURE_ENTITLEMENT_USED, PermissionType::SPECIAL_FEATURE, $category->getPartnerId());
		}
	}
	
	/* (non-PHPdoc)
	 * @see BasecategoryKuser::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		parent::postUpdate($con);
		
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
	}
	
	/**
	 * @param array $permissionNames
	 * @return array
	 */
	public static function removeCategoryPermissions (array $permissionNames)
	{
		$return = array();
		foreach ($permissionNames as $permissionName)
		{
			if ($permissionName != PermissionName::CATEGORY_CONTRIBUTE && $permissionName != PermissionName::CATEGORY_EDIT &&
				$permissionName != PermissionName::CATEGORY_MODERATE && $permissionName != PermissionName::CATEGORY_VIEW)
				{
					$return[] = $permissionName;
				}
		}
		
		return $return;
	}


} // categoryKuser
