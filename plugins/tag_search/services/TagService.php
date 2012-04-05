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
		parent::applyPartnerFilterForClass(new TagPeer());
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
        $tagCoreFilter->attachToCriteria($c);
        $pager->attachToCriteria($c);
        $tags = TagPeer::doSelect($c);
        
        $searchResponse = new KalturaTagListResponse();
        $searchResponse->objects = KalturaTagArray::fromDbArray($tags);
        $searchResponse->totalCount = $c->getRecordsCount();
        
        return $searchResponse;
    }
}