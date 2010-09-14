<?php
chdir(dirname(__FILE__));
require_once("..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."bootstrap.php");

$partner_id = @$argv[1];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
	die('no such partner.'.PHP_EOL);
}


// set category peer default criteria
$criteria_filter = categoryPeer::getCriteriaFilter();
$criteria = $criteria_filter->getFilter();
$criteria->addAnd(categoryPeer::PARTNER_ID, $partner_id);
$criteria_filter->enable();


// query for all partner entries
$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partner_id);
$entries = entryPeer::doSelect($c);


// run over the entries and copy admin tags to categories
foreach($entries as $entry)
{
	$strAdminTags = $entry->getAdminTags();
	$adminTags = explode(',', $entry->getAdminTags());
	if(count($adminTags) > 8)
	{
		// avoid too much tags on single entry
		$adminTags = array_slice($adminTags, 0, 8);
		$strAdminTags = implode(',', $adminTags);
	}
	$entry->setCategories($strAdminTags);
	try
	{
		$entry->save();
		echo $entry->getId() . ": saved ($strAdminTags)\n";
	}
	catch(Exception $ex)
	{
		echo $entry->getId() . ': ' . $ex->getMessage() . " ($strAdminTags)\n";
	}
}

