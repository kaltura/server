<?php

// bootstrap
require_once(__DIR__ . '/../bootstrap.php');

define('MAX_ITEMS', 2000);

function getPartnerUpdates($updatedAt)
{
	$c = new Criteria();
	$c->addSelectColumn(PartnerPeer::ID);
	$c->addSelectColumn(PartnerPeer::STATUS);
	$c->addSelectColumn(PartnerPeer::ADMIN_SECRET);
	$c->addSelectColumn(PartnerPeer::UPDATED_AT);
	$c->add(PartnerPeer::UPDATED_AT, $updatedAt, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn(PartnerPeer::UPDATED_AT);
	$c->setLimit(MAX_ITEMS);
	PartnerPeer::setUseCriteriaFilter(false);
	$stmt = PartnerPeer::doSelectStmt($c);
	PartnerPeer::setUseCriteriaFilter(true);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$maxUpdatedAt = 0;
	$result = array();
	foreach ($rows as $row)
	{
		$id = $row['ID'];
		$status = $row['STATUS'];
		$secret = $status == Partner::PARTNER_STATUS_ACTIVE ? $row['ADMIN_SECRET'] : '';
		$result[$id] = $secret;
		$updatedAt = new DateTime($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, (int)$updatedAt->format('U'));
	}
	
	return array('items' => $result, 'updatedAt' => $maxUpdatedAt);
}

function getLiveUpdates($updatedAt)
{
	// must query sphinx, can be too heavy for the db when the interval is large
	$filter = new entryFilter();
	$filter->setTypeEquel(entryType::LIVE_STREAM);
	$filter->set('_gte_updated_at', $updatedAt);
	$filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
	
	$c = KalturaCriteria::create(entryPeer::OM_CLASS);
	$c->addSelectColumn(entryPeer::ID);
	$c->addSelectColumn(entryPeer::STATUS);
	$c->addSelectColumn(entryPeer::UPDATED_AT);
	$c->addAscendingOrderByColumn(entryPeer::UPDATED_AT);
	$c->setLimit(MAX_ITEMS);
	$c->setMaxRecords(MAX_ITEMS);
	
	$filter->attachToCriteria($c);
	
	entryPeer::setUseCriteriaFilter(false);
	$c->applyFilters();
	$stmt = entryPeer::doSelectStmt($c);
	entryPeer::setUseCriteriaFilter(true);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$maxUpdatedAt = 0;
	$result = array();
	foreach ($rows as $row)
	{
		$id = $row['ID'];
		$status = $row['STATUS'];
		$result[$id] = $status != entryStatus::DELETED ? '1' : '';
		$updatedAt = new DateTime($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, (int)$updatedAt->format('U'));
	}

	return array('items' => $result, 'updatedAt' => $maxUpdatedAt);
}

function getDurationUpdates($updatedAt)
{
	$c = new Criteria();
	$c->addSelectColumn(entryPeer::ID);
	$c->addSelectColumn(entryPeer::STATUS);
	$c->addSelectColumn(entryPeer::LENGTH_IN_MSECS);
	$c->addSelectColumn(entryPeer::UPDATED_AT);
	$c->add(entryPeer::UPDATED_AT, $updatedAt, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn(entryPeer::UPDATED_AT);
	$c->setLimit(MAX_ITEMS);
	entryPeer::setUseCriteriaFilter(false);
	$stmt = entryPeer::doSelectStmt($c);
	entryPeer::setUseCriteriaFilter(true);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$maxUpdatedAt = 0;
	$result = array();
	foreach ($rows as $row)
	{
		$id = $row['ID'];
		$status = $row['STATUS'];
		$duration = intval($row['LENGTH_IN_MSECS'] / 1000);
		$duration = ($status == entryStatus::READY && $duration > 0) ? strval($duration) : '';
		$result[$id] = $duration;
		$updatedAt = new DateTime($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, (int)$updatedAt->format('U'));
	}
	
	return array('items' => $result, 'updatedAt' => $maxUpdatedAt);
}

function getCategoryEntryUpdates($updatedAt)
{
	// get the entry ids
	$c = new Criteria();
	$c->addSelectColumn(categoryEntryPeer::ENTRY_ID);
	$c->addSelectColumn(categoryEntryPeer::UPDATED_AT);
	$c->add(categoryEntryPeer::UPDATED_AT, $updatedAt, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn(categoryEntryPeer::UPDATED_AT);
	$c->setLimit(MAX_ITEMS);
	categoryEntryPeer::setUseCriteriaFilter(false);
	$stmt = categoryEntryPeer::doSelectStmt($c);
	categoryEntryPeer::setUseCriteriaFilter(true);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$maxUpdatedAt = 0;
	$result = array();
	foreach ($rows as $row)
	{
		$entryId = $row['ENTRY_ID'];
		$result[$entryId] = '';
		$updatedAt = new DateTime($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, (int)$updatedAt->format('U'));
	}
	
	// get the categories
	$categoryIdsCol = 'GROUP_CONCAT('.categoryEntryPeer::CATEGORY_FULL_IDS.')';
	$c = new Criteria();
	$c->addSelectColumn(categoryEntryPeer::ENTRY_ID);
	$c->addSelectColumn($categoryIdsCol);
	$c->addGroupByColumn(categoryEntryPeer::ENTRY_ID);
	$c->add(categoryEntryPeer::ENTRY_ID, array_keys($result), Criteria::IN);
	$c->add(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE);
	$stmt = categoryEntryPeer::doSelectStmt($c);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// update the categories (an entry that wasn't fetched in the second query will remain empty)
	foreach ($rows as $row)
	{
		$entryId = $row['ENTRY_ID'];
		$categoryIds = $row[$categoryIdsCol];
		$categoryIds = str_replace('>', ',', $categoryIds);
		$categoryIds = implode(',', array_unique(explode(',', $categoryIds)));
		$result[$entryId] = $categoryIds;
	}
	
	return array('items' => $result, 'updatedAt' => $maxUpdatedAt);
}

// parse params
$params = infraRequestUtils::getRequestParams();
$requestType = isset($params['type']) ? $params['type'] : null;
$updatedAt = isset($params['updatedAt']) ? $params['updatedAt'] : 0;
$token = isset($params['token']) ? $params['token'] : '';
if (!kConf::hasParam('analytics_sync_secret') ||
	$token !== md5(kConf::get('analytics_sync_secret') . $updatedAt))
{
	die;
}

// init database
DbManager::setConfig(kConf::getDB());
DbManager::initialize();

$requestHandlers = array(
	'partner' => 'getPartnerUpdates',
	'live' => 'getLiveUpdates',
	'duration' => 'getDurationUpdates',
	'categoryEntry' => 'getCategoryEntryUpdates',
);

if (isset($requestHandlers[$requestType]))
{
	$result = call_user_func($requestHandlers[$requestType], $updatedAt);
}
else
{
	$result = array('error' => 'bad request');
}

echo json_encode($result);
die;
