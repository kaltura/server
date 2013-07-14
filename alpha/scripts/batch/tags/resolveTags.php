<?php
chdir(dirname(__FILE__));
require_once(__DIR__ . '/../../bootstrap.php');

$c = KalturaCriteria::create(TagPeer::OM_CLASS);
$filter = new TagFilter();
$filter->set('_eq_instance_count', 0);
$filter->attachToCriteria($c);

$count = $c->getRecordsCount();

if (!$count)
	die ('No tags pending for deletion.');
	
TagPeer::setUseCriteriaFilter(false);
$tagsForDelete = TagPeer::doSelect($c);
TagPeer::setUseCriteriaFilter(true);

foreach ($tagsForDelete as $tag)
{
	/* @var $tag Tag */
	switch ($tag->getObjectType())
    {
    	case taggedObjectType::ENTRY:
    		resolveEntryTag($tag);
    		break;
    	case taggedObjectType::CATEGORY:
    		resolveCategoryTag($tag);
    		break;
    }
}

function resolveEntryTag (Tag $tag)
{
    $c = KalturaCriteria::create(entryPeer::OM_CLASS);
    $c->add(entryPeer::PARTNER_ID, $tag->getPartnerId());
    if ($tag->getPrivacyContext() != kTagFlowManager::NULL_PC)
    	$c->addAnd(entryPeer::PRIVACY_BY_CONTEXTS, $tag->getPrivacyContext(), Criteria::LIKE);
		
    $entryFilter = new entryFilter();
    $entryFilter->set('_mlikeand_tags', $tag->getTag());
    $entryFilter->attachToCriteria($c);
    $count = $c->getRecordsCount();
    if (!$count)
    {
    	$tag->delete();	
    }
    
}
    
function resolveCategoryTag (Tag $tag)
{
    $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
    $c->add(categoryPeer::PARTNER_ID, $tag->getPartnerId());
    $categoryFilter = new categoryFilter();
    $categoryFilter->set('_mlikeand_tags', $tag->getTag());
    $categoryFilter->attachToCriteria($c);
    $count = $c->getRecordsCount();
    if (!$count)
    {
    	$tag->delete();	
    }
}