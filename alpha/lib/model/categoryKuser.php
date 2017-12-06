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
class categoryKuser extends BasecategoryKuser implements IIndexable, IElasticIndexable
{
	
	private $old_status = null;

	private $isInInsert = false;
	
	const BULK_UPLOAD_ID = "bulk_upload_id";
	
	const PARTNER_INDEX_PREFIX = 'p';
	
	const UPDATE_METHOD_INDEX_PREFIX = 'um';
	
	const STATUS_INDEX_PREFIX = 'st';
	
	const PERMISSION_NAME_INDEX_PREFIX = "pn";
	
	const PERMISSION_NAME_FIELD_INDEX_PREFIX = "per";
	
	const STATUS_FIELD_PREFIX = "status";

	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

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

	public function updateKuser($puserId = null, $screenName = null) {
		if ($puserId)
			parent::setPuserId($puserId);
		if ($screenName)
			parent::setScreenName($screenName);
	}

	public function setPuserId($puserId)
	{
		if ( $this->getPuserId() == $puserId )  // same value - don't set for nothing 
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
		if ( $this->getKuserId() == $kuserId )  // same value - don't set for nothing 
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
		// no need to update the category if the categoryKuser wasn't updated
		if ($this->isModified())
			$this->updateCategory();
		
		return parent::preUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseObject::preDelete()
	 */
	public function preDelete(PropelPDO $con = null)
	{
		$this->updateCategory(true);

		return parent::preDelete();	
	}

	/* (non-PHPdoc)
	 * @see BasecategoryKuser::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->isInInsert = true;

		return parent::preInsert($con);
	}
	
	private function updateCategory($isDelete = false)
	{
		categoryPeer::setUseCriteriaFilter(false);
		$category = categoryPeer::retrieveByPK($this->category_id);
		categoryPeer::setUseCriteriaFilter(true);
		
		if(!$category)
			throw new kCoreException('category not found');
			
		if ($this->isInInsert)
		{
			if($this->status == CategoryKuserStatus::PENDING)
				$category->setPendingMembersCount($category->getPendingMembersCount() + 1);
			
			if($this->status == CategoryKuserStatus::ACTIVE)
				$category->setMembersCount($category->getMembersCount() + 1);

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
				
		}
		
		if($isDelete)
		{				
			if($this->status == CategoryKuserStatus::PENDING)
				$category->setPendingMembersCount($category->getPendingMembersCount() - 1);
				
			if($this->status == CategoryKuserStatus::ACTIVE)
				$category->setMembersCount($category->getMembersCount() - 1);
				
		}

		$category->save();
		$category->indexCategoryInheritedTree();
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

	public function getIndexObjectName() {
		return "categoryKuserIndex";
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
		return self::STATUS_FIELD_PREFIX. $this->getPartnerId() ." ". self::getSearchIndexFieldValue(categoryKuserPeer::STATUS, $this->getStatus(), $this->getPartnerId());
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

			$this->updateCategory();
		}

		$this->isInInsert = false;
	}
	
	/* (non-PHPdoc)
	 * @see BasecategoryKuser::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		parent::postUpdate($con);
		
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));

		if($this->getColumnsOldValue(categoryKuserPeer::STATUS) != CategoryKuserStatus::DELETED  && $this->getStatus() == CategoryKuserStatus::DELETED)
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
		}
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
	
	public static function getPermissionNamesByPermissionLevel($permissionLevel)
	{
		switch ($permissionLevel)
		{
			case CategoryKuserPermissionLevel::MANAGER:
				$permissionNamesArr[] = PermissionName::CATEGORY_EDIT;
				$permissionNamesArr[] = PermissionName::CATEGORY_MODERATE;
				$permissionNamesArr[] = PermissionName::CATEGORY_CONTRIBUTE;
				$permissionNamesArr[] = PermissionName::CATEGORY_VIEW;
				break;
			case CategoryKuserPermissionLevel::MODERATOR:
				$permissionNamesArr[] = PermissionName::CATEGORY_MODERATE;
				$permissionNamesArr[] = PermissionName::CATEGORY_VIEW;
				break;
			case CategoryKuserPermissionLevel::CONTRIBUTOR:
				$permissionNamesArr[] = PermissionName::CATEGORY_CONTRIBUTE;
				$permissionNamesArr[] = PermissionName::CATEGORY_VIEW;
				break;
			case CategoryKuserPermissionLevel::MEMBER:
				$permissionNamesArr[] = PermissionName::CATEGORY_VIEW;
				break;
		}
		
		return $permissionNamesArr;
	}


	public function getCacheInvalidationKeys()
	{
		return array("categoryKuser:categoryId=".strtolower($this->getCategoryId()));
	}

	/**
	 * return the name of the elasticsearch index for this object
	 */
	public function getElasticIndexName()
	{
		return ElasticIndexMap::ELASTIC_CATEGORY_INDEX;
	}

	/**
	 * return the name of the elasticsearch type for this object
	 */
	public function getElasticObjectType()
	{
		return ElasticIndexMap::ELASTIC_CATEGORY_TYPE;
	}

	/**
	 * return the elasticsearch id for this object
	 */
	public function getElasticId()
	{
		return $this->getCategoryId();
	}

	/**
	 * return the elasticsearch parent id or null if no parent
	 */
	public function getElasticParentId()
	{
		return null;
	}

	/**
	 * get the params we index to elasticsearch for this object
	 */
	public function getObjectParams($params = null)
	{
		$body = array(
			'scripted_upsert' => true,
			'script' => array(
				'inline' => $this->getInlineScript(),
				'lang' => 'painless',
				'params' => array(
					'kuser_id' => $this->getKuserId(),
				)
			),
			'upsert' => new stdClass(),
			'retry_on_conflict' => 10
		);
		return $body;
	}

	private function getInlineScript()
	{
		if($this->getStatus() == CategoryKuserStatus::DELETED)
		{
			$script = 'int idx = ctx._source.kuser_ids.indexOf(params.kuser_id); if(idx != -1) {ctx._source.kuser_ids.remove(idx);}';
		}
		else
		{
			$script = 'if(ctx._source.kuser_ids == null) {ctx._source.kuser_ids = new ArrayList(); ctx._source.kuser_ids.add(params.kuser_id);}';
			$script .= 'else if(!ctx._source.kuser_ids.contains(params.kuser_id)) {ctx._source.kuser_ids.add(params.kuser_id);}';
		}

		return $script;
	}

	/**
	 * return the save method to elastic: ElasticMethodType::INDEX or ElasticMethodType::UPDATE
	 */
	public function getElasticSaveMethod()
	{
		return ElasticMethodType::UPADTE;
	}

	/**
	 * Index the object into elasticsearch
	 */
	public function indexToElastic($params = null)
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForElasticIndexEvent($this));
	}

	/**
	 * return true if the object needs to be deleted from elastic
	 */
	public function shouldDeleteFromElastic()
	{
		return false;
	}

	/**
  * @return partner
  */
        public function getPartner()
        {
                return PartnerPeer::retrieveByPK( $this->getPartnerId() );
        }

/**
	 * return the name of the object we are indexing
	 */
	public function getElasticObjectName()
	{
		return 'category_kuser';
	}
} // categoryKuser
