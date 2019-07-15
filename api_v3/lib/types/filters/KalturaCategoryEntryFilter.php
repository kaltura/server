<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryEntryFilter extends KalturaCategoryEntryBaseFilter
{
	
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new categoryEntryFilter();
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$blockOnEmptyFilterPartners = kConf::getMap(kConfMapNames::REQUIRE_CATEGORY_ENTRY_FILTER_PARTNERS);
		if ($this->entryIdEqual == null &&
			$this->entryIdIn == null &&
			$this->categoryIdIn == null &&
			$this->categoryIdEqual == null && 
			(kEntitlementUtils::getEntitlementEnforcement() || !kCurrentContext::$is_admin_session || in_array(kCurrentContext::getCurrentPartnerId(), $blockOnEmptyFilterPartners))
		)
		{
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_ON_ENTRY_OR_CATEGORY);
		}
			
		if(kEntitlementUtils::getEntitlementEnforcement())
		{
			//validate entitl for entry
			if($this->entryIdEqual != null)
			{
				$entry = entryPeer::retrieveByPK($this->entryIdEqual);
				if(!$entry)
					throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryIdEqual);
			}
			
			//validate entitl for entryIn
			if($this->entryIdIn != null)
			{
				$entry = entryPeer::retrieveByPKs($this->entryIdIn);
				if(!$entry)
					throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryIdIn);
			}
			
			//validate entitl categories
			if($this->categoryIdIn != null)
			{
				$categoryIdInArr = explode(',', $this->categoryIdIn);
				if(!categoryKuserPeer::areCategoriesAllowed($categoryIdInArr))
				$categoryIdInArr = array_unique($categoryIdInArr);
				
				$entitledCategories = categoryPeer::retrieveByPKs($categoryIdInArr);
				
				if(!count($entitledCategories) || count($entitledCategories) != count($categoryIdInArr))
					throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $this->categoryIdIn);
					
				$categoriesIdsUnlisted = array();
				foreach($entitledCategories as $category)
				{
					if($category->getDisplayInSearch() == DisplayInSearchType::CATEGORY_MEMBERS_ONLY)
						$categoriesIdsUnlisted[] = $category->getId();
				}

				if(count($categoriesIdsUnlisted))
				{
					if(!categoryKuserPeer::areCategoriesAllowed($categoriesIdsUnlisted))
						throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $this->categoryIdIn);
				}
			}
			
			//validate entitl category
			if($this->categoryIdEqual != null)
			{
				$category = categoryPeer::retrieveByPK($this->categoryIdEqual);
				if(!$category && kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
					throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $this->categoryIdEqual);

				if(($category->getDisplayInSearch() == DisplayInSearchType::CATEGORY_MEMBERS_ONLY) && 
					!categoryKuserPeer::retrievePermittedKuserInCategory($category->getId(), kCurrentContext::getCurrentKsKuserId()))
				{
					throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $this->categoryIdEqual);
				}
			}
		}
			
		$this->fixUserIds();
		$categoryEntryFilter = $this->toObject();
		 
		$c = KalturaCriteria::create(categoryEntryPeer::OM_CLASS);
		$categoryEntryFilter->attachToCriteria($c);
		
		if(!kEntitlementUtils::getEntitlementEnforcement() || $this->entryIdEqual == null)
			$pager->attachToCriteria($c);
			
		//When filtering createdAtGreaterThanOrEqual add updated at filtering with the same value to utilize an existing index
		if(isset($this->createdAtGreaterThanOrEqual))
			$c->addAnd(categoryEntryPeer::UPDATED_AT, $this->createdAtGreaterThanOrEqual, Criteria::GREATER_EQUAL);
		
		$dbCategoriesEntry = categoryEntryPeer::doSelect($c);
		
		if(kEntitlementUtils::getEntitlementEnforcement() && count($dbCategoriesEntry) && $this->entryIdEqual != null)
		{
			//remove unlisted categories: display in search is set to members only
			$categoriesIds = array();
			foreach ($dbCategoriesEntry as $dbCategoryEntry)
				$categoriesIds[] = $dbCategoryEntry->getCategoryId();
				
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$c->add(categoryPeer::ID, $categoriesIds, Criteria::IN);
			$pager->attachToCriteria($c);
			$c->applyFilters();
			
			$categoryIds = $c->getFetchedIds();
			
			foreach ($dbCategoriesEntry as $key => $dbCategoryEntry)
			{
				if(!in_array($dbCategoryEntry->getCategoryId(), $categoryIds))
				{
					KalturaLog::info('Category [' . print_r($dbCategoryEntry->getCategoryId(),true) . '] is not listed to user');
					unset($dbCategoriesEntry[$key]);
				}
			}
			
			$totalCount = $c->getRecordsCount();
		}
		else
		{
			$resultCount = count($dbCategoriesEntry);
			if ($resultCount && $resultCount < $pager->pageSize)
				$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
			else
			{
				KalturaFilterPager::detachFromCriteria($c);
				$totalCount = categoryEntryPeer::doCount($c);
			}
		}
			
		$categoryEntrylist = KalturaCategoryEntryArray::fromDbArray($dbCategoriesEntry, $responseProfile);
		$response = new KalturaCategoryEntryListResponse();
		$response->objects = $categoryEntrylist;
		$response->totalCount = $totalCount; // no pager since category entry is limited to ENTRY::MAX_CATEGORIES_PER_ENTRY
		return $response;
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::validateForResponseProfile()
	 */
	public function validateForResponseProfile()
	{
		if(kEntitlementUtils::getEntitlementEnforcement())
		{
			if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENABLE_RESPONSE_PROFILE_USER_CACHE, kCurrentContext::getCurrentPartnerId()))
			{
				KalturaResponseProfileCacher::useUserCache();
				return;
			}
			
			throw new KalturaAPIException(KalturaErrors::CANNOT_LIST_RELATED_ENTITLED_WHEN_ENTITLEMENT_IS_ENABLE, get_class($this));
		}
	}
	
	private function fixUserIds ()
	{
		if ($this->creatorUserIdEqual !== null)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->creatorUserIdEqual);
			if ($kuser)
				$this->creatorUserIdEqual = $kuser->getId();
			else 
				$this->creatorUserIdEqual = -1; // no result will be returned when the user is missing
		}

		if(!empty($this->creatorUserIdIn))
		{
			$this->creatorUserIdIn = $this->preparePusersToKusersFilter( $this->creatorUserIdIn );
		}
	}
}
