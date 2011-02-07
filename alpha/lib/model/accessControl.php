<?php

/**
 * Subclass for representing a row from the 'access_control' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class accessControl extends BaseaccessControl
{
	/**
	 * True when set as partner default (saved on partner object)
	 * 
	 * @var bool
	 */
	protected $isDefault;
	
	/**
	 * @var accessControlScope
	 */
	protected $scope;

	public function save(PropelPDO $con = null)
	{
		if ($this->isColumnModified(accessControlPeer::DELETED_AT))
		{
			if ($this->isDefault === true)
				throw new Exception("Default access control profile can't be deleted");
				
			$c = new Criteria();
			$c->add(entryPeer::ACCESS_CONTROL_ID, $this->getId());
			$entryCount = entryPeer::doCount($c);
			if ($entryCount > 0)
				throw new Exception("Access control profile is linked with entries and can't be deleted");
		}
		
		if ($this->isNew())
		{
			$c = new Criteria();
			$c->add(accessControlPeer::PARTNER_ID, $this->partner_id);
			$count = accessControlPeer::doCount($c);
			
			if ($count >= Partner::MAX_ACCESS_CONTROLS)
			{
				throw new kCoreException("Max number of access control profiles was reached", kCoreException::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED);
			}
		}
		
		parent::save($con);
		
		// set this conversion profile as partners default
		$partner = PartnerPeer::retrieveByPK($this->partner_id);
		if ($partner && $this->isDefault === true)
		{
			$partner->setDefaultAccessControlId($this->getId());
			$partner->save();
		}
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseaccessControl#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(accessControlPeer::DELETED_AT) && !is_null($this->getDeletedAt()))
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setPartnerId($this->partner_id);
		$copyObj->setName($this->name);
		$copyObj->setDescription($this->description);
		$copyObj->setCreatedAt($this->created_at);
		$copyObj->setUpdatedAt($this->updated_at);
		$copyObj->setDeletedAt($this->deleted_at);
		$copyObj->setNew(true);
		$copyObj->setId(NULL);
		 
		$copyObj->clearRestrictions($this->getRestrictions());
		$copyObj->setIsDefault($this->getIsDefault());
	}
	
	/**
	 * Set the accessControlScope
	 * 
	 * @param $scope
	 */
	public function setScope(accessControlScope $scope)
	{
		$this->scope = $scope;
	}
	
	/**
	 * Get the accessControlScope
	 * 
	 * @return accessControlScope
	 */
	public function getScope()
	{
		if (!$this->scope instanceof accessControlScope)
			$this->scope = new accessControlScope();
			
		return $this->scope;
	}
	
	/**
	 * check of there are any restrictions in this accessControl object
	 * if there any restrictions return true, otherwise return false (if the acessControl is a default one) 
	 * 
	 * @return boolean
	 */
	public function hasRestrictions()
	{
		return
		(
			$this->hasSiteRestriction() ||
			$this->hasCountryRestriction() ||
			$this->hasSessionRestriction() ||
			$this->hasPreviewRestriction() ||
			$this->hasDirectoryRestriction()
		);
	}
	
	/**
	 * Get all the restrictions
	 * 
	 * @return array
	 */
	public function getRestrictions()
	{
		$restrictions = array();
		
		if ($this->hasSiteRestriction())
			$restrictions[] = $this->getSiteRestriction();
			
		if ($this->hasCountryRestriction())
			$restrictions[] = $this->getCountryRestriction();
			
		if ($this->hasSessionRestriction())
			$restrictions[] = $this->getSessionRestriction();
			
		if ($this->hasPreviewRestriction())
			$restrictions[] = $this->getPreviewRestriction();
			
		if ($this->hasDirectoryRestriction())
			$restrictions[] = $this->getDirectoryRestriction();
			
		return $restrictions;
	}
	
	/**
	 * Set new restrictions list
	 *  
	 * @param array $restrictions
	 */
	public function setRestrictions($restrictions)
	{
		$this->clearRestrictions();
		if (is_array($restrictions))
		{
			foreach($restrictions as $restriction)
			{
				$this->setRestriction($restriction);
			}
		}
	}
	
	/**
	 * Clear all the restrictions
	 */
	public function clearRestrictions()
	{
		parent::setSiteRestrictType(null);
		parent::setSiteRestrictList(null);
		parent::setCountryRestrictType(null);
		parent::setCountryRestrictList(null);
		parent::setKsRestrictPrivilege(null);
		parent::setPrvRestrictPrivilege(null);
		parent::setPrvRestrictLength(null);
		parent::setKdirRestrictType(null);
	}
	
	/**
	 * Set restriction
	 * 
	 * @param $restriction
	 */
	public function setRestriction(baseRestriction $restriction)
	{
		$restrictionClass = get_class($restriction);
		switch($restrictionClass)
		{
			case "siteRestriction":
				$this->setSiteRestriction($restriction);
				break;
			case "countryRestriction":
				$this->setCountryRestriction($restriction);
				break;
			case "sessionRestriction":
				$this->setSessionRestriction($restriction);
				break;
			case "previewRestriction":
				$this->setPreviewRestriction($restriction);
				break;
			case "directoryRestriction":
				$this->setDirectoryRestriction($restriction);
				break;
		}
	}
	
	/**
	 * Validate all the restrictions using the accessControlScope
	 *
	 * @return bool
	 */
	public function isValid()
	{
		if (!$this->scope instanceof accessControlScope)
			throw new Exception("Scope was not set");
			
		// if we have ks
		if ($this->scope->getKs() && ($this->scope->getKs() instanceof ks))
		{
			// not need to validate if we have an admin ks
			if ($this->scope->getKs()->isAdmin())
				return true;
		}
			
		$restrictions = $this->getRestrictions();
		foreach($restrictions as $restriction)
		{
			if ($restriction->isValid() === false) // if one is not valid, all access control considered not valid
				return false;
		}
		
		return true;
	}
	
	public function getSiteRestriction()
	{
		if (!$this->hasSiteRestriction())
			return null;
			
		$restriction = new siteRestriction($this); 
		$restriction->setType(parent::getSiteRestrictType());
		$restriction->setSiteList(parent::getSiteRestrictList());
		
		return $restriction;
	}
	
	public function hasSiteRestriction()
	{
		return (parent::getSiteRestrictType() !== null);
	}
	
	public function setSiteRestriction(siteRestriction $restriction)
	{
		parent::setSiteRestrictType($restriction->getType());
		parent::setSiteRestrictList($restriction->getSiteList());
	}
	
	public function getCountryRestriction()
	{
		if (!$this->hasCountryRestriction())
			return null;
			
		$restriction = new countryRestriction($this);
		$restriction->setType(parent::getCountryRestrictType());
		$restriction->setCountryList(parent::getCountryRestrictList());
		
		return $restriction;
	}
	
	public function hasCountryRestriction()
	{
		return (parent::getCountryRestrictType() !== null);
	}
	
	public function setCountryRestriction(countryRestriction $restriction)
	{
		parent::setCountryRestrictType($restriction->getType());
		parent::setCountryRestrictList($restriction->getCountryList());
	}
	
	public function getSessionRestriction()
	{
		if (!$this->hasSessionRestriction())
			return null;
			
		$restriction = new sessionRestriction($this);
		$restriction->setPrivilegeName(parent::getKsRestrictPrivilege());
		
		return $restriction;
	}
	
	public function hasSessionRestriction()
	{
		return (parent::getKsRestrictPrivilege() !== null);
	}
	
	public function setSessionRestriction(sessionRestriction $restriction)
	{
		parent::setKsRestrictPrivilege($restriction->getPrivilegeName());
	}
	
	public function getPreviewRestriction()
	{
		if (!$this->hasPreviewRestriction())
			return null;
			
		$restriction = new previewRestriction($this);
		$restriction->setPrivilegeName(parent::getPrvRestrictPrivilege());
		$restriction->setPreviewLength(parent::getPrvRestrictLength());
		
		return $restriction;
	}
	
	public function hasPreviewRestriction()
	{
		return (parent::getPrvRestrictPrivilege() !== null);
	}
	
	public function setPreviewRestriction(previewRestriction $restriction)
	{
		parent::setPrvRestrictPrivilege($restriction->getPrivilegeName());
		parent::setPrvRestrictLength($restriction->getPreviewLength());
	}
	
	public function getDirectoryRestriction()
	{
		if (!$this->hasDirectoryRestriction())
			return null;
			
		$restriction = new directoryRestriction($this);
		$restriction->setType(parent::getKdirRestrictType());
		
		return $restriction;
	}

	public function hasDirectoryRestriction()
	{
		return (parent::getKdirRestrictType() !== null);
	}
	
	public function setDirectoryRestriction(directoryRestriction $restrinction)
	{
		parent::setKdirRestrictType($restrinction->getType());
	}
	
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
			if ($partner && ($this->getId() == $partner->getDefaultAccessControlId()))
				$this->isDefault = true;
			else
				$this->isDefault = false;
		}
		
		return $this->isDefault;
	}
	
	function setSiteRestrictType($v)
	{
		throw new Exception("Internal use only");		
	}
	
	function setSiteRestrictList($v)
	{
		throw new Exception("Internal use only");		
	}
	
	function setCountryRestrictType($v)
	{
		throw new Exception("Internal use only");		
	}
	
	function setCountryRestrictList($v)
	{
		throw new Exception("Internal use only");		
	}
	
	function setKsRestrictPrivilege($v)
	{
		throw new Exception("Internal use only");		
	}
	
	function setPrvRestrictPrivilege($v)
	{
		throw new Exception("Internal use only");		
	}
	
	function setPrvRestrictLength($v)
	{
		throw new Exception("Internal use only");		
	}
	
	function setKdirRestrictType($v)
	{
		throw new Exception("Internal use only");		
	}
}
