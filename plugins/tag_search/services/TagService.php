<?php
/**
 * Search object tags
 *
 * @service tag
 * @package plugins.tagSearch
 * @subpackage api.services
 */
class TagService extends KalturaBaseService
{   
    public function initService($serviceId, $serviceName, $actionName)
    {
        parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('Tag');
    }
    
    /**
     * @action search
     * 
     * Action to search tags using a string of 3 letters or more.
     * @param KalturaTagFilter $tagFilter
     * @param KalturaFilterPager $pager
     * @return KalturaTagListResponse
     */
    public function searchAction (KalturaTagFilter $tagFilter, KalturaFilterPager $pager = null)
    {
        if (!$tagFilter)
        {
            $tagFilter = new KalturaTagFilter();
        }
        
        if (!$pager)
        {
            $pager = new KalturaFilterPager();
        }
        
        $tagFilter->validate();

        $c = KalturaCriteria::create(TagPeer::OM_CLASS);
        $tagCoreFilter = new TagFilter();
        $tagFilter->toObject($tagCoreFilter);
        $c->setGroupByColumn('tag');
        $tagCoreFilter->attachToCriteria($c);
        $pager->attachToCriteria($c);
        $tags = TagPeer::doSelect($c);
        
        $searchResponse = new KalturaTagListResponse();
        $searchResponse->objects = KalturaTagArray::fromDbArray($tags, $this->getResponseProfile());
        $searchResponse->totalCount = $c->getRecordsCount();
        
        return $searchResponse;
    }
    
    /**
     * Action goes over all tags with instanceCount==0 and checks whether they need to be removed from the DB. Returns number of removed tags.
     * @action deletePending
     * @return int
     */
    public function deletePendingAction ()
    {
		TagPeer::setUseCriteriaFilter(false);
    	$c = KalturaCriteria::create(TagPeer::OM_CLASS);
		$filter = new TagFilter();
		$filter->set('_eq_instance_count', 0);
		$filter->attachToCriteria($c);
		$c->applyFilters();
		$count = $c->getRecordsCount();
		
		if (!$count)
		{
			KalturaLog::info ('No tags pending for deletion.');
			return 0;
		}
			
		$deletedTags = 0;
		$tagsForDelete = TagPeer::doSelect($c);
		TagPeer::setUseCriteriaFilter(true);
		
		foreach ($tagsForDelete as $tag)
		{
			/* @var $tag Tag */
			switch ($tag->getObjectType())
		    {
		    	case taggedObjectType::ENTRY:
		    		$deletedTags += $this->resolveEntryTag($tag);
		    		break;
		    	case taggedObjectType::CATEGORY:
		    		$deletedTags += $this->resolveCategoryTag($tag);
		    		break;
		    }
		}
		
		return $deletedTags;
    }
    
	/**
	 * @param Tag $tag
	 * @return int
	 */
	private function resolveEntryTag (Tag $tag)
	{
	    $c = KalturaCriteria::create(entryPeer::OM_CLASS);
	    $c->add(entryPeer::PARTNER_ID, $tag->getPartnerId());
	    if ($tag->getPrivacyContext() != kTagFlowManager::NULL_PC)
	    	$c->addAnd(entryPeer::PRIVACY_BY_CONTEXTS, $tag->getPrivacyContext(), Criteria::LIKE);
			
	    $entryFilter = new entryFilter();
	    $tagString = str_replace(kTagFlowManager::$specialCharacters, kTagFlowManager::$specialCharactersReplacement, $tag->getTag());
	    $entryFilter->set('_mlikeand_tags', $tagString);
	    $entryFilter->attachToCriteria($c);
	    $c->applyFilters();
	    $count = $c->getRecordsCount();
	    if (!$count)
	    {
	    	$tag->delete();	
	    	return 1;
	    }
		else
		{
			$tag->setInstanceCount($count);
			$tag->save();
			return 1;		
		}
	    
	    return 0;
	    
	}
	    
	/**
	 * @param Tag $tag
	 * @return int
	 */
	private function resolveCategoryTag (Tag $tag)
	{
	    $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
	    $c->add(categoryPeer::PARTNER_ID, $tag->getPartnerId());
	    $categoryFilter = new categoryFilter();
	    $tagString = str_replace(kTagFlowManager::$specialCharacters, kTagFlowManager::$specialCharactersReplacement, $tag->getTag());
	    $categoryFilter->set('_mlikeand_tags', $tagString);
	    $categoryFilter->attachToCriteria($c);
	    $c->applyFilters();
	    $count = $c->getRecordsCount();
	    if (!$count)
	    {
	    	$tag->delete();	
	    	return 1;
	    }
	    else
		{
			$tag->setInstanceCount($count);
			$tag->save();
			return 1;		
		}
		
	    return 0;
	}
	
	/**
	 * @action indexCategoryEntryTags
	 * 
	 * @param int $categoryId 
	 * @param string $pcToDecrement
	 * @param string $pcToIncrement
	 */
	public function indexCategoryEntryTagsAction ($categoryId, $pcToDecrement, $pcToIncrement)
	{
		$pcToDecrementArray = explode(',', $pcToDecrement);
		$c = KalturaCriteria::create(TagPeer::OM_CLASS);
		$c->add(TagPeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId());
		$c->add(TagPeer::PRIVACY_CONTEXT, $pcToDecrementArray, KalturaCriteria::IN);
		TagPeer::setUseCriteriaFilter(false);
		$tagsToDecrement = TagPeer::doSelect($c);
		TagPeer::setUseCriteriaFilter(true);	
		foreach ($tagsToDecrement as $tag)
		{
			/* @var $tag Tag */
			$tag->decrementInstanceCount();
		}

		if(!$pcToIncrement)
		{
			return;
		}

		$pcToIncrementArray = explode(',', $pcToIncrement);
		$tagsToIncrement = array();
		$c = new Criteria();
		$c->add(categoryEntryPeer::CATEGORY_ID, $categoryId);
		$catEntries = categoryEntryPeer::doSelect($c);
		foreach ($catEntries as $catEntry)
		{
			/* @var $catEntry categoryEntry */
			$entry = entryPeer::retrieveByPK($catEntry->getEntryId());
			if($entry)
				$tagsToIncrement = array_merge($tagsToIncrement, explode(',', $entry->getTags()));
		}
		
		$tagsToIncrement = array_unique($tagsToIncrement);
		kTagFlowManager::addOrIncrementTags(implode(",", $tagsToIncrement), kCurrentContext::getCurrentPartnerId(), "entry", $pcToIncrementArray);
	}
	
}