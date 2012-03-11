<?php
class KSecureEntryHelper
{
	/**
	 * 
	 * @var entry
	 */
	private $entry;
	
	/**
	 * 
	 * @var string
	 */
	private $ksStr;
	
	/**
	 * 
	 * @var ks
	 */
	private $ks;
	
	/**
	 * 
	 * @var string
	 */
	private $referrer;
	
	/**
	 * Indicates what contexts should be tested 
	 * No contexts means any context
	 * 
	 * @var array of accessControlContextType
	 */
	private $contexts;
	
	/**
	 * Indicates that access control need to be checked every request and therefore can't be cached
	 * 
	 * @var bool
	 */
	private $disableCache;
	
	
	/**
	 * 
	 * @param entry $entry
	 */
	public function __construct(entry $entry, $ksStr, $referrer, $contexts = array())
	{
		if(!is_array($contexts))
			$contexts = array($contexts);
			
		$this->entry = $entry;
		$this->ksStr = $ksStr;
		$this->referrer = $referrer;
		$this->contexts = $contexts;
		
		$this->validateKs();
	}
	
	public function hasRules()
	{
		$accessControl = $this->entry->getAccessControl();
		if ($accessControl)
			return $accessControl->hasRules();
			
		return false;
	}
	
	public function shouldPreview()
	{
		if ($this->isKsAdmin())
			return false;
		
		if ($this->isEntryInModeration()) // don't preview when entry is in moderation
			return false;
			
		// should preview only when the access control is valid, but the preview and session restrictions exists
		$accessControl = $this->entry->getAccessControl();
		if ($accessControl)
		{
			$context = new kEntryContextDataResult();
			$accessControl->applyContext($context, $this->getAccessControlScope());
			
			$actions = $context->getAccessControlActions();
			$previewActionFound = false;
			foreach($actions as $action)
			{
				/* @var $action kAccessControlAction */
				if($action->getType() == accessControlActionType::BLOCK)
					return false;
					
				if($action->getType() == accessControlActionType::PREVIEW)
					$previewActionFound = true;
			}
			return $previewActionFound;
		}
		return false;
	}
	
	public function getPreviewLength()
	{
		$accessControl = $this->entry->getAccessControl();
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
		if ($this->ks)
		{
			if ($this->isKsAdmin()) // no need to validate when ks is admin
				return;
			
			if ($this->ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, ks::PRIVILEGE_WILDCARD)) // no need to validate when we have wildcard download privilege
				return;
				
			if ($this->ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, $this->entry->getId())) // no need to validate when we have specific entry download privilege
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
		$accessControl = $this->entry->getAccessControl();
		if(!$accessControl)
			return;
			
		$context = new kEntryContextDataResult();
		$this->disableCache = $accessControl->applyContext($context, $this->getAccessControlScope());
		if(count($context->getAccessControlActions()))
			KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED);
	}
	
	protected function validateScheduling()
	{
		if (!$this->entry->isScheduledNow() && !$this->isKsAdmin())
		{
			KExternalErrors::dieError(KExternalErrors::NOT_SCHEDULED_NOW);
		}
	}
	
	protected function validateKs()
	{
		if ($this->ksStr)
		{
			try
			{
				// todo need to check if partner is within a partner group
				$ks = kSessionUtils::crackKs($this->ksStr);
				// if entry is "display_in_search=2" validate partner ID from the KS
				// => meaning it will alwasy pass on partner_id
				if($this->entry->getDisplayInSearch() != mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK)
				{
					$valid = $ks->isValidForPartner($this->entry->getPartnerId());
				}
				else
				{
					$valid = $ks->isValidForPartner($ks->partner_id);
				}
				if ($valid === ks::EXPIRED)
					die("This URL is expired");
				else if ($valid === ks::INVALID_PARTNER)
				{
					if ($this->hasRules()) // TODO - for now if the entry doesnt have restrictions any way disregard a partner group check
						die("Invalid session [".$valid."]");
				}
				else if ($valid !== ks::OK)
				{
					die("Invalid session [".$valid."]");
				}
				
				if ($ks->partner_id != $this->entry->getPartnerId())
				{
					return;
				}
					
				$this->ks = $ks;	
			}
			catch(Exception $ex)
			{
				KExternalErrors::dieError(KExternalErrors::INVALID_KS_SRT);
			}
		}
	}
	
	public function isKsAdmin()
	{
		 return ($this->ks && $this->ks->isAdmin());
	}
	
	protected function isEntryInModeration()
	{
		$entry = $this->entry;
		$moderationStatus = $entry->getModerationStatus();
		$invalidModerationStatuses = array(
			entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION, 
			entry::ENTRY_MODERATION_STATUS_REJECTED
		);
		
		return in_array($moderationStatus, $invalidModerationStatuses);
	}
	
	/**
	 * Access control need to be checked every request and therefore request can't be cached
	 * @return boolean
	 */
	public function shouldDisableCache()
	{
		return $this->disableCache;
	}
	
	private function getAccessControlScope()
	{
		$scope = new accessControlScope();
		if ($this->referrer)
			$scope->setReferrer($this->referrer);
		$scope->setKs($this->ks);
		$scope->setEntryId($this->entry->getId());
		$scope->setContexts($this->contexts);
		return $scope;
	}

}