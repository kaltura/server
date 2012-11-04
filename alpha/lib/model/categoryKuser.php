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
	
	public function setStatus($v)
	{
		$this->old_status = $this->getStatus();

		parent::setStatus($v);
	}
	
	/**
	 * Code to be run before persisting the object
	 * @param PropelPDO $con
	 * @return bloolean
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		$this->updateCategroy();
		
		return parent::preUpdate($con);
	}
	
	public function preDelete(PropelPDO $con = null)
	{
		$this->updateCategroy(true);
		
		return parent::preDelete();	
	}	
	
	/**
	 * Code to be run before persisting the object
	 * @param PropelPDO $con
	 * @return bloolean
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
		return categoryKuserPeer::getOMClass(false);
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap() {
		return array (
			'int_id' => 'intId',
			'category_id' => 'categoryId',
			'kuser_id' => 'kuserId',
			'category_full_ids' => 'categoryFullIds',
			'permission_names' => 'permissionNames',
			'created_at' => 'createdAt',
		);
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldType()
	 */
	public function getIndexFieldType($field) {
		return array (
			'int_id' => IIndexable::FIELD_TYPE_INTEGER,
			'category_id' => IIndexable::FIELD_TYPE_INTEGER,
			'kuser_id' => IIndexable::FIELD_TYPE_INTEGER,
			'category_full_ids' => IIndexable::FIELD_TYPE_STRING,
			'permission_names' => IIndexable::FIELD_TYPE_STRING,
			'created_at' => IIndexable::FIELD_TYPE_DATETIME
		);
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getSearchIndexFieldsEscapeType()
	 */
	public function getSearchIndexFieldsEscapeType($field) {
		
		return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex() {
		
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}

} // categoryKuser
