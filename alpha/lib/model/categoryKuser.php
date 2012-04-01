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
	
	public function setPuserId($puserId)
	{
		if ( self::getPuserId() == $puserId )  // same value - don't set for nothing 
			return;

		parent::setPuserId($puserId);
			
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $puserId);
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
	
	
	public function save(PropelPDO $con = null)
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
		}
		
		if (($this->isColumnModified(categoryKuserPeer::STATUS) && !$this->isNew()))
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
		
		$category->save();
		
		parent::save($con);
		
		//TODO add job to 
		//update inheritance categories with membersCount from parent (propel objects) and this will also 
		// reindex those categoryies with the new members.
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
} // categoryKuser
