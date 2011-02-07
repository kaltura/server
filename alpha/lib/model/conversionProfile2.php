<?php

/**
 * Subclass for representing a row from the 'conversion_profile_2' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class conversionProfile2 extends BaseconversionProfile2
{
	const CONVERSION_PROFILE_NONE = -1;
	
	const CONVERSION_PROFILE_2_CREATION_MODE_MANUAL = 1;
	const CONVERSION_PROFILE_2_CREATION_MODE_KMC = 2;
	const CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC = 3;
	const CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC_BYPASS_FLV = 4;
	
	
	protected $isDefault;
	

	public function setIsDefault($v)
	{
		$this->isDefault = (bool)$v;
	}
	
	public function getIsDefault()
	{
		if ($this->isDefault === null)
		{
			if ($this->isNew())
				return false;
				
			$partner = PartnerPeer::retrieveByPK($this->partner_id);
			if ($partner && ($this->getId() == $partner->getDefaultConversionProfileId()))
				$this->isDefault = true;
			else
				$this->isDefault = false;
		}
		
		return $this->isDefault;
	}
	
	public function save(PropelPDO $con = null)
	{
		if ($this->isColumnModified(conversionProfile2Peer::DELETED_AT) && $this->isDefault === true)
		{
			throw new Exception("Default conversion profile can't be deleted");
		}
		parent::save($con);
		
		// set this conversion profile as partners default
		$partner = PartnerPeer::retrieveByPK($this->partner_id);
		if ($partner && $this->isDefault === true)
		{
			$partner->setDefaultConversionProfileId($this->getId());
			$partner->save();
		}
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseconversionProfile2#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(conversionProfile2Peer::DELETED_AT) && !is_null($this->getDeletedAt()))
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	public function copyInto($copyObj, $deepCopy = false)
	{
		parent::copyInto($copyObj, $deepCopy);
		$copyObj->setIsDefault($this->getIsDefault());
	}
	
	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this conversionProfile2 is new, it will return
	 * an empty collection; or if this conversionProfile2 has previously
	 * been saved, it will retrieve related flavorParamsConversionProfiles from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in conversionProfile2.
	 */
	public function getflavorParamsConversionProfilesJoinflavorParams($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		return $this->getflavorParamsConversionProfilesJoinassetParams($criteria, $con, $join_behavior);
	}
	
}
