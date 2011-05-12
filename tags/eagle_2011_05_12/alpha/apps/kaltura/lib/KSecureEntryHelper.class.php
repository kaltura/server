<?php
class KSecureEntryHelper
{
	/**
	 * 
	 * @var entry
	 */
	private $_entry;
	
	/**
	 * 
	 * @var string
	 */
	private $_ksStr;
	
	/**
	 * 
	 * @var ks
	 */
	private $_ks;
	
	/**
	 * 
	 * @var string
	 */
	private $_referrer;
	
	
	/**
	 * 
	 * @param entry $entry
	 */
	public function __construct(entry $entry, $ksStr, $referrer)
	{
		$this->_entry = $entry;
		$this->_ksStr = $ksStr;
		$this->_referrer = $referrer;
		
		$this->validateKs();
	}
	
	public function hasRestrictions()
	{
		$accessControl = $this->_entry->getAccessControl();
		if ($accessControl)
		{
			$restrictions = $accessControl->getRestrictions();
			if (count($restrictions))
				return true;
		}
		
		return false;
	}
	
	public function shouldPreview()
	{
		if ($this->isKsAdmin())
			return false;
		
		if ($this->isEntryInModeration()) // don't preview when entry is in moderation
			return false;
			
		// should preview only when the access control is valid, but the preview and session restrictions exists
		$accessControl = $this->_entry->getAccessControl();
		if ($accessControl)
		{
			$accessControlScope = $this->getAccessControlScope();
			$accessControl->setScope($accessControlScope);
			if ($accessControl->isValid()) // the whole access control is valid, no need to preview
			{
				return false; 
			}
			else
			{
				$restrictions = $accessControl->getRestrictions();
				// all restrictions should be valid except for previewRestriction & sessionRestriction 
				foreach($restrictions as $restriction)
				{
					if (!($restriction instanceof previewRestriction) && !($restriction instanceof sessionRestriction))
						if ($restriction->isValid() === false)
							return false;
				}
				if ($accessControl->hasPreviewRestriction())
				{
					$restriction = $accessControl->getPreviewRestriction();
					if ($restriction->isValid() === false)
						return true;
				}
			}
		}
		return false;
	}
	
	public function getPreviewLength()
	{
		$accessControl = $this->_entry->getAccessControl();
		if ($accessControl)
		{
			if ($accessControl->hasPreviewRestriction())
				return $accessControl->getPreviewRestriction()->getPreviewLength();
			else
				return null;
		}
	}
	
	public function validateForPlay()
	{
		$this->validateModeration();
		$this->validateScheduling();
		$this->validateAccessControl();
	}
	
	public function validateForDownload()
	{
		if ($this->_ks)
		{
			if ($this->isKsAdmin()) // no need to validate when ks is admin
				return;
			
			if ($this->_ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, ks::PRIVILEGE_WILDCARD)) // no need to validate when we have wildcard download privilege
				return;
				
			if ($this->_ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, $this->_entry->getId())) // no need to validate when we have specific entry download privilege
				return;
		}	
			
		$this->validateForPlay();
	}
	
	protected function validateModeration()
	{
		if ($this->isKsAdmin()) // no need to validate when ks is admin
			return;
			
		if ($this->isEntryInModeration())
			KExternalErrors::dieError(KExternalErrors::ENTRY_MODERATION_ERROR);
	}
	
	protected function validateAccessControl()
	{
		$accessControl = $this->_entry->getAccessControl();
		if ($accessControl)
		{
			$accessControlScope = $this->getAccessControlScope();
			$accessControl->setScope($accessControlScope);
			
			if (!$accessControl->isValid())
			{
				KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED);
			}
		}
	}
	
	protected function validateScheduling()
	{
		if (!$this->_entry->isScheduledNow() && !$this->isKsAdmin())
		{
			KExternalErrors::dieError(KExternalErrors::NOT_SCHEDULED_NOW);
		}
	}
	
	protected function validateKs()
	{
		if ($this->_ksStr)
		{
			try
			{
				// todo need to check if partner is within a partner group
				$ks = kSessionUtils::crackKs($this->_ksStr);
				// if entry is "display_in_search=2" validate partner ID from the KS
				// => meaning it will alwasy pass on partner_id
				if($this->_entry->getDisplayInSearch() != mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK)
				{
					$valid = $ks->isValidForPartner($this->_entry->getPartnerId());
				}
				else
				{
					$valid = $ks->isValidForPartner($ks->partner_id);
				}
				if ($valid === ks::EXPIRED)
					die("This URL is expired");
				else if ($valid === ks::INVALID_PARTNER)
				{
					if ($this->hasRestrictions()) // todo - for now if the entry doesnt have restrictions any way disregard a partner group check
						die("Invalid session [".$valid."]");
				}
				else if ($valid !== ks::OK)
				{
					die("Invalid session [".$valid."]");
				}
					
				$this->_ks = $ks;	
			}
			catch(Exception $ex)
			{
				KExternalErrors::dieError(KExternalErrors::INVALID_KS_SRT);
			}
		}
	}
	
	public function isKsAdmin()
	{
		 return ($this->_ks && $this->_ks->isAdmin());
	}
	
	protected function isEntryInModeration()
	{
		$entry = $this->_entry;
		$moderationStatus = $entry->getModerationStatus();
		$invalidModerationStatuses = array(
			entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION, 
			entry::ENTRY_MODERATION_STATUS_REJECTED
		);
		
		return in_array($moderationStatus, $invalidModerationStatuses);
	}
	
	private function getAccessControlScope()
	{
		$accessControlScope = accessControlScope::partialInit();
		if ($this->_referrer)
			$accessControlScope->setReferrer($this->_referrer);
		$accessControlScope->setKs($this->_ks);
		$accessControlScope->setEntryId($this->_entry->getId());
		return $accessControlScope;
	}

}