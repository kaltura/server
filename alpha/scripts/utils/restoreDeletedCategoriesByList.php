<?php
chdir(__DIR__ . '/../');
require_once 'bootstrap.php'; 

myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

if($argc != 4)
{
        KalturaLog::debug ("Usage: php restoreCategoriesByList.php [partnerId] [categoryListfileName] [dryRun]");
	die("Not enough parameters" . PHP_EOL);
}

$partnerId = $argv[1] ;
$categoryListfileName = $argv[2];

//should the script save() ? by default will not save
$dryRun= $argv[3] !== 'realRun';
KalturaStatement::setDryRun($dryRun);
if ($dryRun)
	KalturaLog::debug("dry run --- in order to save give realRun as a second parameter");

$categoriesIds = file($categoryListfileName);
$categoriesIds = array_map('trim',$categoriesIds);

$categoryEntryCriteria = new Criteria();
$categoryEntryCriteria->add(categoryEntryPeer::PARTNER_ID, $partnerId);

$categories = categoryPeer::retrieveByPKsNoFilter($categoriesIds);

foreach ($categories as $category)
{
	$categoryId = $category->getId();
	KalturaLog::debug('iterating category id - ' . $categoryId);
	if ($category->getPartnerId() != $partnerId)
	{
		KalturaLog::debug('category of a different partner - ' . $category->getPartnerId());
		continue;
	}
	
	$category->setStatus(CategoryStatus::ACTIVE);
	$category->save();
	
	//reviving categoryEntries
	$categoryEntryCriteria->add(categoryEntryPeer::CATEGORY_ID , $categoryId );
	categoryEntryPeer::setUseCriteriaFilter(false);
	$categoryEntries = categoryEntryPeer::doSelect($categoryEntryCriteria);
	categoryEntryPeer::setUseCriteriaFilter(true);
	
	$categoryEntryCriteria->remove(categoryEntryPeer::CATEGORY_ID);

	$categoryEntryCounter = 0;
	foreach($categoryEntries as $categoryEntry)
	{
		$categoryEntryId = $categoryEntry->getId();
		KalturaLog::debug('iterating categoryEntry id - ' . $categoryEntryId);
	
		$entryId = $categoryEntry->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);

		if (!$entry)
		{
		        KalturaLog::debug('no Active entry for categoryEntry id - ' . $categoryEntryId);
		        continue;
		}

		if ($categoryEntry->getStatus() != CategoryEntryStatus::ACTIVE)
		{
			$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
			$categoryEntry->save();
			$categoryEntryCounter++;
		}
	}
	KalturaLog::debug('category entries revived - ' . $categoryEntryCounter . ' - for category ' . $categoryId);

	//this code segment should be tested & added
	/*
	if ($category->getPrivacyContexts())
	{
		$categoryKuserCriteria = new Criteria();
		$categoryKuserCriteria->add(categoryKuserPeer::PARTNER_ID, $partnerId);
		$categoryKuserCriteria->add(categoryKuserPeer::CATEGORY_ID , $categoryId );
		categoryKuserPeer::setUseCriteriaFilter(false);
		$categorykusers = categoryKuserPeer::doSelect($categoryKuserCriteria);
		categoryKuserPeer::setUseCriteriaFilter(true);
		
		$categoryKuserCounter = 0;
		foreach($categorykusers as $categorykuser)
		{
			if ($categorykuser->getStatus() != CategoryKuserStatus::ACTIVE)
			{
				$categorykuser->setStatus(CategoryKuserStatus::ACTIVE);
				$categorykuser->save();
				$categoryKuserCounter++;
			}
			
		}
		KalturaLog::debug('category kusers revived - ' . $categoryKuserCounter . ' - for category ' . $categoryId);
		
	}
*/
}
