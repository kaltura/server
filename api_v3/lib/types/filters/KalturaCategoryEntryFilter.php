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
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		if ($this->entryIdEqual == null &&
			$this->categoryIdIn == null &&
			$this->categoryIdEqual == null && 
			(kEntitlementUtils::getEntitlementEnforcement() || !kCurrentContext::$is_admin_session))
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_ON_ENTRY_OR_CATEGORY);		
			
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
			
		$categoryEntryFilter = $this->toObject();
		 
		$c = KalturaCriteria::create(categoryEntryPeer::OM_CLASS);
		$categoryEntryFilter->attachToCriteria($c);
		
		if(!kEntitlementUtils::getEntitlementEnforcement() || $this->entryIdEqual == null)
			$pager->attachToCriteria($c);
			
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
					KalturaLog::debug('Category [' . print_r($dbCategoryEntry->getCategoryId(),true) . '] is not listed to user');
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
				$totalCount = categoryEntryPeer::doCount($c);
		}
			
		$categoryEntrylist = KalturaCategoryEntryArray::fromDbArray($dbCategoriesEntry, $responseProfile);
		$response = new KalturaCategoryEntryListResponse();
		$response->objects = $categoryEntrylist;
		$response->totalCount = $totalCount; // no pager since category entry is limited to ENTRY::MAX_CATEGORIES_PER_ENTRY
		return $response;
	}
}
