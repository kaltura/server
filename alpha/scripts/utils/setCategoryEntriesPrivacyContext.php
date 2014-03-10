<?php
/**
 * update privacyContext on categoryEntry
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true; //TODO: change for real run
if (in_array ( 'realrun', $argv ))
	$dryRun = false;

	
$countLimitEachLoop = 200;
//------------------------------------------------------


require_once (dirname ( __FILE__ ) . '/../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$lastCategoryId = 0;

while ( 1 ) 
{
	$c = new Criteria ();
	$c->add ( categoryPeer::STATUS, CategoryStatus::ACTIVE, Criteria::EQUAL );
	$c->add ( categoryPeer::ID, $lastCategoryId, Criteria::GREATER_THAN );
	$c->add ( categoryPeer::PRIVACY_CONTEXTS, null, Criteria::ISNOTNULL);
	$c->add ( categoryPeer::PRIVACY_CONTEXTS, '', Criteria::NOT_EQUAL );
	$c->addAscendingOrderByColumn ( categoryPeer::ID );
	$c->setLimit ( $countLimitEachLoop );
	$categories = categoryPeer::doSelect ( $c, $con );
    
	if (!count($categories))
    	break;

	foreach ( $categories as $category ) 
	{
		/* @var $category category */
		KalturaLog::debug('Category ['.$category->getId().']');
		$lastCategoryEntryId = 0;
		while(1)
		{
			$c = new Criteria ();
			$c->add ( categoryEntryPeer::CATEGORY_ID, $category->getId(), Criteria::EQUAL );
			$c->add ( categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE, Criteria::EQUAL );
			$c->add ( categoryEntryPeer::ID, $lastCategoryEntryId, Criteria::GREATER_THAN );
			$c->addAscendingOrderByColumn ( categoryEntryPeer::ID );
			$c->setLimit ( $countLimitEachLoop );
			$categoryEntries = categoryEntryPeer::doSelect ( $c, $con );

			if (!count($categoryEntries))
    			break;

    		foreach ($categoryEntries as $categoryEntry) 
    		{
    			/* @var $categoryEntry categoryEntry */
    			try 
    			{
    				$categoryEntry->setPrivacyContext($category->getPrivacyContexts());
    				$categoryEntry->save();
    			}
    			catch (Exception $e)
    			{
    				KalturaLog::debug('failed to update category entry '.$categoryEntry->getId());
    			}
    			$lastCategoryEntryId = $categoryEntry->getId();
    		}
		}
        $lastCategoryId = $category->getId();
	}

	kMemoryManager::clearMemory();
	sleep ( 1 );
}

KalturaLog::debug("Done");
