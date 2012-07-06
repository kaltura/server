<?php
/**
 * Call flavor assete setters to migrate from old columns to new custom data fields.
 * After all flavors will be migrated we can remove the columns from the db.
 *
 * @package Deployment
 * @subpackage updates
 */ 

$countLimitEachLoop = 500;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
$c = new Criteria();

if (isset($argv[1]))
{
    $c->addAnd(entryPeer::INT_ID, $argv[1], Criteria::GREATER_EQUAL);
}
if (isset($argv[2]))
{
    $c->addAnd(entryPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
}

if (isset($argv[3]))
{
    $c->addAnd(entryPeer::UPDATED_AT, $argv[3], Criteria::GREATER_EQUAL);
}

$c->addAnd(entryPeer::TAGS, null, KalturaCriteria::NOT_EQUAL);
$c->setLimit($countLimitEachLoop);
$c->addAscendingOrderByColumn(entryPeer::UPDATED_AT);
$entryResults = entryPeer::doSelect($c, $con);

while($entryResults && count($entryResults))
{
	foreach($entryResults as $entry)
	{
		/* @var $entry entry */
	    $entryTags = trimObjectTags($entry->getTags());
	    
	    if (!count($entryTags))
	    {
	        continue;
	    }
	    
	    $c = kTagFlowManager::getTagObjectsByTagStringsCriteria($entryTags, taggedObjectType::ENTRY, $entry->getPartnerId());
	    $c->applyFilters();
	    
	    $numTagsFound = $c->getRecordsCount(); 
	   
	    if (!$numTagsFound)
	    {
	        $requiredTags = $entryTags;
	    }
	    else 
	    {
    	    $crit = new Criteria();
    	    $crit->addAnd(TagPeer::ID , $c->getFetchedIds(), KalturaCriteria::IN);
    	    $foundTagObjects = TagPeer::doSelect($crit);
    	    $foundTags = array();
    	    foreach ($foundTagObjects as $foundTag)
    	    {
    	        $foundTag->incrementInstanceCount();
    	        $foundTags[] = $foundTag->getTag();
    	    }
    	    
    	    $requiredTags = array_diff($entryTags, $foundTags);
	    
	    }
	    
	    foreach ($requiredTags as $tagString)
	    {
	        $tag = new Tag();
	        $tag->setTag($tagString);
	        $tag->setPartnerId($entry->getPartnerId());
	        $tag->setObjectType(taggedObjectType::ENTRY);
	        $tag->save();
	    }
		
	}
	$countLimitEachLoop += $countLimitEachLoop;
	$c->setOffset($countLimitEachLoop);
	$entryResults = entryPeer::doSelect($c, $con);
	usleep(100);
}

function trimObjectTags ($tagsString)
{
    $arr = explode(",", $tagsString);
    for ($i=0; $i < count($arr); $i++)
    {
        $arr[$i] = trim($arr[$i]);
    }
    return $arr;
}
