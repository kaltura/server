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
class categoryKuser extends BasecategoryKuser {
	
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
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $this->userId);
			
		parent::setKuserId($kuser->getId());
	}
	
	public function setKuserId($kuserId)
	{
		if ( self::getKuserId() == $kuserId )  // same value - don't set for nothing 
			return;

		parent::setKuserId($kuserId);
			
		$kuser = kuserPeer::retrieveByPK($kuserId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $this->userId);
			
		parent::setPuserId($kuser->getPuserId());
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
	public function postUpdate(PropelPDO $con = null)
	{
		$this->updateCategroy();
		
		return parent::postUpdate($con);
	}
	
	/**
	 * Code to be run before persisting the object
	 * @param PropelPDO $con
	 * @return bloolean
	 */
	public function postInsert(PropelPDO $con = null)
	{
		$this->updateCategroy();
		
		return parent::postInsert($con);
	}
	
	private function updateCategroy()
	{
		$category = categoryPeer::retrieveByPK($this->category_id);
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
		
		$this->addIndexCategoryInheritedTreeJob($category->getFullIds());
		$category->indexToSearchIndex();
	}
	
	public function addIndexCategoryInheritedTreeJob($fullIdsStartsWithCategoryId)
	{
		$featureStatusToRemoveIndex = new kFeatureStatus();
		$featureStatusToRemoveIndex->setType(FeatureStatusType::INDEX_CATEGORY);
		
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

	
	public function delete(PropelPDO $con = null)
	{
		$category = categoryPeer::retrieveByPK($this->category_id);
		if(!$category)
			throw new kCoreException('category not found');
			
		if($this->status == CategoryKuserStatus::PENDING)
			$category->setPendingMembersCount($category->getPendingMembersCount() - 1);
			
		if($this->status == CategoryKuserStatus::ACTIVE)
			$category->setMembersCount($category->getMembersCount() - 1);
			
		$category->save();
		
		parent::delete($con);
	}
	
	public function reSetCategoryFullIds()
	{
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		if(!$category)
			throw new kCoreException('category id [' . $this->getCategoryId() . 'was not found', kCoreException::ID_NOT_FOUND);
			
		$this->setCategoryFullIds($category->getFullIds());
	}
	
	//	set properties in custom data
	
    public function setBulkUploadId ($bulkUploadId){$this->putInCustomData (self::BULK_UPLOAD_ID, $bulkUploadId);}
	public function getBulkUploadId (){return $this->getFromCustomData(self::BULK_UPLOAD_ID);}
} // categoryKuser
