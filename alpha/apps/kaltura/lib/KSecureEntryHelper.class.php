<?php
/**
 * @package Core
 * @subpackage utils
 *
 */
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
	 * @var array
	 */
	private $hashes;
	
	/**
	 * the result of applyContext
	 * @var kEntryContextDataResult
	 */
	private $contextResult;
	
	/**
	 * Indicates if the context has LIMIT_FLAVORS action
	 * @var bool
	 */
	private $hasLimitFlavorsAction = false;
	
	/**
	 * Indicates if the context has BLOCKED action
	 * @var bool
	 */
	private $hasBlockAction = false;

	/**
	 * Indicates if the context has PREVIEW action
	 * @var bool
	 */	
	private $hasPreviewAction = false;
	
	/**
	 * 
	 * @var kAccessControlLimitFlavorsAction
	 */
	private $limitFlavorsAction = null;
	
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
		$this->applyContext();
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
		if ($this->contextResult)
		{			
			if($this->hasBlockAction)
				return false;
					
			return $this->hasPreviewAction;
		}
		return false;
	}
	
	public function getPreviewLength()
	{
		$preview = null;
		if ($this->contextResult && $this->hasPreviewAction)
		{			
			$actions = $this->contextResult->getAccessControlActions();
			foreach($actions as $action)
			{
				if($action instanceof kAccessControlPreviewAction)
					$preview = $preview ? min($preview, $action->getLimit()) : $action->getLimit();
			}
		}
		return $preview;
	}

	public function validateApiAccessControl()
	{
		$partner = $this->entry->getPartner();
		if ($partner && !$partner->validateApiAccessControl())
			KExternalErrors::dieError(KExternalErrors::SERVICE_ACCESS_CONTROL_RESTRICTED);
	}

	public function validateForPlay($performApiAccessCheck = true)
	{
	    if ($this->contexts != array(accessControlContextType::THUMBNAIL))
	    {
		    $this->validateModeration();
			$this->validateScheduling();
	    }
		$this->validateAccessControl($performApiAccessCheck);
	}
	
	public function validateForDownload()
	{
		$this->validateApiAccessControl();
		
		if ($this->ks)
		{
			if ($this->isKsAdmin()) // no need to validate when ks is admin
				return;
			
			if ($this->ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, ks::PRIVILEGE_WILDCARD)) // no need to validate when we have wildcard download privilege
				return;
				
			if ($this->ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, $this->entry->getId())) // no need to validate when we have specific entry download privilege
				return;
		}	
			
		$this->validateForPlay(false);
	}
	
	protected function validateModeration()
	{
		if ($this->isKsAdmin()) // no need to validate when ks is admin
			return;
			
		if ($this->isEntryInModeration())
			KExternalErrors::dieError(KExternalErrors::ENTRY_MODERATION_ERROR);
	}
	
	public function validateAccessControl($performApiAccessCheck = true)
	{
		if ($performApiAccessCheck)
		{
			$this->validateApiAccessControl();
		}
		
		if(!$this->contextResult)
			return;
			
		if(count($this->contextResult->getAccessControlMessages()))
		{
			foreach($this->contextResult->getAccessControlMessages() as $msg)
				header("X-Kaltura: access-control: $msg");
		}
		
		if($this->hasBlockAction)
		{
			KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED);
		}
	}
	
	public function filterAllowedFlavorParams(array $flavorParamsIds)
	{
		if($this->hasLimitFlavorsAction)
		{
			$filteredFlavorParamsIds = array();
			foreach ($flavorParamsIds as $flavorParamsId) 
			{
				if($this->isFlavorParamsAllowed($flavorParamsId))
					$filteredFlavorParamsIds[] = $flavorParamsId;
			}
			return $filteredFlavorParamsIds;
		}
		return $flavorParamsIds;
	}
	
	public function isAssetAllowed(asset $asset)
	{
		return $this->isFlavorParamsAllowed($asset->getFlavorParamsId());		
	}
	
	public function shouldBlock()
	{
		return $this->hasBlockAction;
	}
	
	protected function isFlavorParamsAllowed($flavorParamsId)
	{
		if($this->hasLimitFlavorsAction)
		{
			$flavorParamsIds = explode(',', $this->limitFlavorsAction->getFlavorParamsIds());
			$exists = in_array($flavorParamsId, $flavorParamsIds);
			if($this->limitFlavorsAction->getIsBlockedList())
				return !$exists;
			else 
				return $exists;
		}
		return true;
	}
	
	protected function applyContext()
	{
		$this->contextResult = null;
		$accessControl = $this->entry->getAccessControl();
		if(!$accessControl)
			return;
			
		$this->contextResult = new kEntryContextDataResult();
		$scope = $this->getAccessControlScope();
		if (!$this->isKsAdmin())
			$this->disableCache = $accessControl->applyContext($this->contextResult, $scope);
		else
			$this->disableCache = false;
			
		if(count($this->contextResult->getAccessControlActions()))
		{
			$actions = $this->contextResult->getAccessControlActions();
			foreach($actions as $action)
			{
				/* @var $action kAccessControlAction */
				switch ($action->getType())
				{
					case accessControlActionType::BLOCK:
						$this->hasBlockAction = true;
						break;
					case accessControlActionType::LIMIT_FLAVORS:
						$this->hasLimitFlavorsAction = true;
						$this->limitFlavorsAction = $action;
						break;
					case accessControlActionType::PREVIEW:
						$this->hasPreviewAction = true;
						break;					
				}
			}
		}			
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
					KExternalErrors::dieError(KExternalErrors::KS_EXPIRED, "This URL is expired");
				else if ($valid === ks::INVALID_PARTNER)
				{
					if ($this->hasRules()) // TODO - for now if the entry doesnt have restrictions any way disregard a partner group check
						KExternalErrors::dieError(KExternalErrors::INVALID_PARTNER, "Invalid session [".$valid."]");
				}
				else if ($valid !== ks::OK)
				{
					KExternalErrors::dieError(KExternalErrors::INVALID_KS, "Invalid session [".$valid."]");
				}
				
				if ($ks->partner_id != $this->entry->getPartnerId() && $ks->partner_id != Partner::BATCH_PARTNER_ID)
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
	
	public function isKsWidget()
	{
		 return (!$this->ksStr || ($this->ks && $this->ks->isWidgetSession()));
	}
	
	/**
	 * Indicates that the KS user is the owner of the entry
	 * @return bool
	 */
	protected function isKsUserOwnsEntry()
	{
		return (!$this->isKsWidget() && $this->ks && $this->entry && $this->entry->getKuserId() == $this->ks->getKuserId());
	}
	
	
	/**
	 * Indicates that the entry is not approved
	 * @return bool
	 */
	public function isEntryInModeration()
	{
		$entry = $this->entry;
		$moderationStatus = $entry->getModerationStatus();
		$invalidModerationStatuses = array(
			entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION, 
			entry::ENTRY_MODERATION_STATUS_REJECTED
		);
		
		if(!in_array($moderationStatus, $invalidModerationStatuses))
			return false;
			
		if($this->isKsAdmin() || $this->isKsUserOwnsEntry())
			return false;
			
		return true;
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
		$accessControlScope = new accessControlScope();
		if ($this->referrer)
			$accessControlScope->setReferrer($this->referrer);
		$accessControlScope->setKs($this->ks);
		$accessControlScope->setEntryId($this->entry->getId());
		$accessControlScope->setContexts($this->contexts);
		$accessControlScope->setHashes($this->hashes);
		
		return $accessControlScope;
	}

	public function setHashes (array $hashes)
	{
		$this->hashes = $hashes;
	}
}