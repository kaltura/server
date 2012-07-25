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
	$categoryDb->reSetFullIds();
	$categoryDb->reSetInheritedParentId();
	$categoryDb->reSetDepth();
	$categoryDb->reSetFullName();
	$categoryDb->reSetEntriesCount();
	$categoryDb->reSetMembersCount();
	$categoryDb->reSetPendingMembersCount();
	$categoryDb->reSetPrivacyContext();
	$categoryDb->reSetDirectSubCategoriesCount();
	$categoryDb->reSetDirectEntriesCount();	
	$categoryDb->save();
}