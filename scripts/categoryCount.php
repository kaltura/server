<?php
ini_set("memory_limit","256M");

require_once 'bootstrap.php';


if(!count($argv))
	die("No partner_id passed to script!");
	
$partnerId = $argv[1];

var_dump($partnerId);

if ( !PartnerPeer::retrieveByPK($partnerId) )
	die("Please enter a valid partner Id!");

$criteria = new Criteria();
$criteria->add(categoryPeer::PARTNER_ID,$partnerId,Criteria::EQUAL);
$allCats = categoryPeer::doSelect($criteria);


foreach ($allCats as $cat)
{
	/* @var $cat category */
	$allChildren = $cat->getAllChildren();
	$allSubCatsIds = array();
	$allSubCatsIds[] = $cat->getId();
	
	if (count($allChildren))
	{
		foreach ($allChildren as $child)
			$allSubCatsIds[] = $child->getId();	
	}

	$c = KalturaCriteria::create(entryPeer::OM_CLASS);
	$entryFilter = new entryFilter();
	$entryFilter->set("_matchor_categories_ids", implode(',',$allSubCatsIds));
	$entryFilter->attachToCriteria($c);
	$entriesCount = count (entryPeer::doSelect($c));
	
	echo "Number of entries in category <". $cat->getName().">: ". $entriesCount ."\r\n";
	
	$cat->setEntriesCount($entriesCount);
	$cat->save();
}