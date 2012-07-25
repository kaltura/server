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


foreach ($allCats as $categoryDb)
{
	$categoryDb->reSetEntriesCount();
	$categoryDb->reSetDirectSubCategoriesCount();
	$categoryDb->reSetDirectEntriesCount();	
	$categoryDb->save();
}