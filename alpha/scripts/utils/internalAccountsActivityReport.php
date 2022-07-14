<?php
require_once(__DIR__ . '/../bootstrap.php');
if ($argc < 2)
{
	die("Usage: php internalAccountsActivityReport.php outputPath [ignoreTemplatePartnerId]"."\n");
}
$outputPath = $argv[1];
// In case a template partner has internalUse active, we wish to ignore it for partners created from the template
// For this we can use the script using $ignoreTemplatePartnerId
$ignoreTemplatePartnerId = null;
if (isset($argv[2]))
{
	$ignoreTemplatePartnerId = $argv[2];
}
// Equivalent:
// $ignoreTemplatePartnerId = $argv[2] ?? null;

const INTERNAL_KALTURA_EMAIL = '@kaltura.com';
const PARTNERS_LIMIT = 500;

$partnersResults = array();
$lastPartnerId = 0;

do
{
	$partnersFromDb = getPartnersFromDb();
	foreach ($partnersFromDb as $partner)
	{
		$partnerInfo = getBasicPartnerColumns($partner);
		if (!isInternalPartner($partnerInfo, $ignoreTemplatePartnerId))
		{
			continue;
		}
		getLastEntryActionsForPartner($partnerInfo);
		
		$partnersResults = array_merge($partnersResults, $partnerInfo);
	}
}
while (sizeof($partnersFromDb) == PARTNERS_LIMIT);
writeResultsToCsv($outputPath, $partnersResults);


function getPartnersFromDb()
{
	global $lastPartnerId;
	
	$c = new Criteria();
	$c->addSelectColumn(PartnerPeer::ID);
	$c->addSelectColumn(PartnerPeer::ADMIN_EMAIL);
	$c->addSelectColumn(PartnerPeer::CREATED_AT);
	$c->addSelectColumn(PartnerPeer::UPDATED_AT);
	$c->addSelectColumn(PartnerPeer::PARTNER_PACKAGE);
	$c->addSelectColumn(PartnerPeer::CUSTOM_DATA);
	$c->setLimit(PARTNERS_LIMIT);
	$c->addAnd(PartnerPeer::ID, $lastPartnerId, Criteria::GREATER_THAN);
	
	// = PartnerPeer::doSelectStmt($c);
	//$partnersFromDb = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$partnersFromDb = PartnerPeer::doSelect($c);
	print_r($partnersFromDb);
	
//	$lastPartnerId = end($partnersFromDb)['ID'];
	$lastPartnerId = end($partnersFromDb)->getId();
	
	return $partnersFromDb;
}

function getBasicPartnerColumns($partner)
{
//	$customData = myCustomData::fromString($partner['CUSTOM_DATA']);
//	$partnerInfo = array('id' => $partner['ID'], 'email' => $partner['ADMIN_EMAIL'], 'createdAt' => $partner['CREATED_AT'], 'updatedAt' => $partner['UPDATED_AT'],
//		'partnerPackage' => $partner['PARTNER_PACKAGE'], 'internalUseEnabled' => $customData->get('internalUse'), 'templatePartnerId' => $customData->get('i18n_template_partner_id'));
	$partnerInfo = array('id' => $partner->getId(), 'email' => $partner->getAdminEmail(), 'createdAt' => $partner->getCreatedAt(), 'updatedAt' => $partner->getUpdatedAt(),
		'partnerPackage' => $partner->getPartnerPackage(), 'internalUseEnabled' => $partner->getFromCustomData('internalUse'), 'templatePartnerId' => $partner->getFromCustomData('i18n_template_partner_id'));
	
	return $partnerInfo;
}

function isInternalPartner(&$partnerInfo, $ignoreTemplatePartnerId)
{
	if (isset($ignoreTemplatePartnerId))
	{
		$partnerInfo['internalUseEnabled'] = ($partnerInfo['internalUseEnabled'] && $partnerInfo['templatePartnerId'] != $ignoreTemplatePartnerId) ? 1 : 0;
	}
	else
	{
		$partnerInfo['internalUseEnabled'] = $partnerInfo['internalUseEnabled'] ? 1 : 0;
	}
	$partnerInfo['internalPartnerPackage'] = $partnerInfo['partnerPackage'] == PartnerPackages::PARTNER_PACKAGE_INTERNAL_TRIAL ? 1 : 0;
	$partnerInfo['internalKalturaEmail'] = strpos($partnerInfo['email'], INTERNAL_KALTURA_EMAIL) ? 1 : 0;
	
	if ($partnerInfo['internalUseEnabled'] || $partnerInfo['internalPartnerPackage'] || $partnerInfo['internalKalturaEmail'])
	{
		return true;
	}
	
	return false;
}

function getLastEntryActionsForPartner(&$partnerInfo)
{
	kCurrentContext::$partner_id = $partnerInfo['id'];
	
	$coreResults = searchForLatestOfFieldName(ESearchEntryFieldName::CREATED_AT, ESearchEntryOrderByFieldName::CREATED_AT);
	if ($coreResults[0]->getObject())
	{
		$partnerInfo['lastEntryCreatedAt'] = $coreResults[0]->getObject()->getCreatedAt();
	}
	
	$coreResults = searchForLatestOfFieldName(ESearchEntryFieldName::LAST_PLAYED_AT, ESearchEntryOrderByFieldName::LAST_PLAYED_AT);
	if ($coreResults[0]->getObject())
	{
		$partnerInfo['lastEntryViewedAt'] = $coreResults[0]->getObject()->getLastPlayedAt();
	}
}

function searchForLatestOfFieldName($fieldName, $fieldNameOrderBy)
{
	$range = new ESearchRange();
	$range->setLessThanOrEqual(time());
	$entryItem = new ESearchEntryItem();
	$entryItem->setItemType(ESearchItemType::RANGE);
	$entryItem->setFieldName($fieldName);
	$entryItem->setRange($range);
	$searchItems = array($entryItem);
	$operator = new ESearchOperator();
	$operator->setOperator(ESearchOperatorType::AND_OP);
	$operator->setSearchItems($searchItems);
	$pager = new kPager();
	$pager->setPageSize(1);
	$entryOrderBy = new ESearchEntryOrderByItem();
	$entryOrderBy->setSortField($fieldNameOrderBy);
	$entryOrderBy->setSortOrder(ESearchSortOrder::ORDER_BY_DESC);
	$orderByItems = array($entryOrderBy);
	$orderBy = new ESearchOrderBy();
	$orderBy->setOrderItems($orderByItems);
	
	$entrySearch = new kEntrySearch();
	$eSearchResults = $entrySearch->doSearch($operator, $pager, array(), null, $orderBy);
	list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($eSearchResults, $entrySearch);
	
	return $coreResults;
}

function writeResultsToCsv($outputPath, $results)
{
	$dataToWrite = array();
	$headerRow = array('partnerId', 'email', 'created_at', 'updated_at', 'partner_package', 'is_internal_use_enabled', 'template_partner_id', 'is_partner_package_internal', 'is_internal_kaltura_email', 'last_entry_created_at', 'last_entry_viewed_at');
	$dataToWrite[] = $headerRow;
	foreach  ($results as $result)
	{
		$dataToWrite[] = $result;
	}
	
	$file = fopen($outputPath, 'w');
	if (!$file)
	{
		KalturaLog::err("Error: Failed to create file $outputPath");
		return;
	}
	
	foreach ($dataToWrite as $row)
	{
		fputcsv($file, $row);
	}
	fclose($file);
}