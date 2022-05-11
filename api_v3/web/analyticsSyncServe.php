<?php

// bootstrap
require_once(__DIR__ . '/../bootstrap.php');

define('MAX_ITEMS', 2000);

define('PARTNER_SECRET', 's');
define('PARTNER_CRM_ID', 'ci');
define('PARTNER_VERTICAL', 'v');
define('PARTNER_PARENT_ID', 'pp');

define('ENTRY_KUSER_ID', 'ku');
define('ENTRY_TYPE', 't');
define('ENTRY_MEDIA_TYPE', 'mt');
define('ENTRY_SOURCE_TYPE', 'st');
define('ENTRY_DURATION', 'd');
define('ENTRY_CREATOR_ID', 'c');
define('ENTRY_CREATED_AT', 'ca');

define('SOURCE_CLASSROOM', -10);
define('SOURCE_CAPTURE', -11);
define('SOURCE_PITCH', -12);
define('SOURCE_WEBCAST', -13);
define('SOURCE_RAPT', -14);
define('SOURCE_WEBEX', -15);
define('SOURCE_ZOOM', -16);
define('SOURCE_EXPRESS_RECORDER', -17);
define('SOURCE_KMS_NATIVE_ANDROID_APP', -18);
define('SOURCE_KMS_NATIVE_IOS_APP', -19);
define('SOURCE_EXTERNAL_YOUTUBE', -20);
define('SOURCE_MEETING', -21);
define('SOURCE_ONEDRIVE', -22);


define('CREATED_DAY_TS', 'UNIX_TIMESTAMP(DATE(CREATED_AT))');

$sourceFromAdminTag = array(
	'kalturaclassroom' => SOURCE_CLASSROOM,
	'kalturacapture' => SOURCE_CAPTURE,
	'videomessage' => SOURCE_PITCH,
	'kms-webcast-event' => SOURCE_WEBCAST,
	'raptentry' => SOURCE_RAPT,
	'webexentry' => SOURCE_WEBEX,
	'zoomentry' => SOURCE_ZOOM,
	'expressrecorder' => SOURCE_EXPRESS_RECORDER,
	'kmsnativeandroid' => SOURCE_KMS_NATIVE_ANDROID_APP,
	'kmsnativeios' => SOURCE_KMS_NATIVE_IOS_APP,
	'kalturameeting' => SOURCE_MEETING,
	'onedrive' => SOURCE_ONEDRIVE,
);

$externalSources = array(
	'YouTube' => SOURCE_EXTERNAL_YOUTUBE,
);

function getPartnerVertical($customData)
{
	if (isset($customData['internalUse']) && $customData['internalUse'])
	{
		return -1;
	}
	else if (isset($customData['verticalClasiffication']) && $customData['verticalClasiffication'] > 0)
	{
		return $customData['verticalClasiffication'];
	}
	else
	{
		return 0;
	}
}

function getEntrySourceTypeInt($sourceType, $adminTags, $customData)
{
	global $sourceFromAdminTag, $externalSources;

	// check for specific admin tags
	$adminTags = explode(',', strtolower($adminTags));
	foreach ($adminTags as $adminTag)
	{
		$adminTag = trim($adminTag);
		if (isset($sourceFromAdminTag[$adminTag]))
		{
			return $sourceFromAdminTag[$adminTag];
		}
	}

	// check for external source
	if (isset($customData['externalSource']) && isset($externalSources[$customData['externalSource']]))
	{
		return $externalSources[$customData['externalSource']];
	}

	// use the source type
	return $sourceType;
}

function getUnixTimestampFromDate($date)
{
	$dt = new DateTime($date);
	return (int) $dt->format('U');
}

function getPartnerUpdates($updatedAt)
{
	$c = new Criteria();
	$c->addSelectColumn(PartnerPeer::ID);
	$c->addSelectColumn(PartnerPeer::STATUS);
	$c->addSelectColumn(PartnerPeer::ADMIN_SECRET);
	$c->addSelectColumn(PartnerPeer::CUSTOM_DATA);
	$c->addSelectColumn(PartnerPeer::PARTNER_PARENT_ID);
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
		if ($status == Partner::PARTNER_STATUS_ACTIVE)
		{
			$customData = unserialize($row['CUSTOM_DATA']);
			
			$info = array(
				PARTNER_SECRET => $row['ADMIN_SECRET'],
				PARTNER_CRM_ID => isset($customData['crmId']) ? $customData['crmId'] : '',
				PARTNER_VERTICAL => getPartnerVertical($customData),
				PARTNER_PARENT_ID => $row['PARTNER_PARENT_ID'],
			);
			$info = json_encode($info);
		}
		else
		{
			$info = '';
		}
		
		$result[$id] = $info;
		$updatedAt = new DateTime($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, (int)$updatedAt->format('U'));
	}
	
	return array('items' => $result, 'updatedAt' => $maxUpdatedAt, 'totalCount' => count($rows));
}

function getUserUpdates($updatedAt)
{
	$c = new Criteria();
	$c->addSelectColumn(kuserPeer::ID);
	$c->addSelectColumn(kuserPeer::STATUS);
	$c->addSelectColumn(kuserPeer::PUSER_ID);
	$c->addSelectColumn(kuserPeer::PARTNER_ID);
	$c->addSelectColumn(kuserPeer::UPDATED_AT);
	$c->add(kuserPeer::UPDATED_AT, $updatedAt, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn(kuserPeer::UPDATED_AT);
	$c->setLimit(MAX_ITEMS);
	kuserPeer::setUseCriteriaFilter(false);
	$stmt = kuserPeer::doSelectStmt($c);
	kuserPeer::setUseCriteriaFilter(true);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$maxUpdatedAt = 0;
	$result = array();
	foreach ($rows as $row)
	{
		$status = $row['STATUS'];
		if ($status == KuserStatus::DELETED)
		{
			continue;
		}

		$id = $row['ID'];
		$puserId = $row['PUSER_ID'];
		$partnerId = $row['PARTNER_ID'];
		
		$key = md5($partnerId . '_' . strtolower($puserId));
		$result[$key] = $id;
		$updatedAt = new DateTime($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, (int)$updatedAt->format('U'));
	}

	return array('items' => $result, 'updatedAt' => $maxUpdatedAt, 'totalCount' => count($rows));
}

function getEntryUpdates($updatedAt)
{
	$c = new Criteria();
	$c->addSelectColumn(entryPeer::ID);
	$c->addSelectColumn(entryPeer::STATUS);
	$c->addSelectColumn(entryPeer::LENGTH_IN_MSECS);
	$c->addSelectColumn(entryPeer::KUSER_ID);
	$c->addSelectColumn(entryPeer::TYPE);
	$c->addSelectColumn(entryPeer::MEDIA_TYPE);
	$c->addSelectColumn(entryPeer::SOURCE);
	$c->addSelectColumn(entryPeer::ADMIN_TAGS);
	$c->addSelectColumn(CREATED_DAY_TS);
	$c->addSelectColumn(entryPeer::CUSTOM_DATA);
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
		if ($status == entryStatus::READY)
		{
			$customData = unserialize($row['CUSTOM_DATA']);

			$info = array(
				ENTRY_KUSER_ID => $row['KUSER_ID'],
				ENTRY_TYPE => $row['TYPE'],
				ENTRY_MEDIA_TYPE => $row['MEDIA_TYPE'],
				ENTRY_SOURCE_TYPE => getEntrySourceTypeInt($row['SOURCE'], $row['ADMIN_TAGS'], $customData),
				ENTRY_CREATED_AT => $row[CREATED_DAY_TS],
				ENTRY_CREATOR_ID => isset($customData['creatorKuserId']) ? $customData['creatorKuserId'] : $row['KUSER_ID'],
			);
			$duration = intval($row['LENGTH_IN_MSECS'] / 1000);
			if ($duration > 0)
			{
				$info[ENTRY_DURATION] = $duration;
			}
			$info = json_encode($info);
		}
		else
		{
			$info = '';
		}

		$result[$id] = $info;		
		$updatedAt = getUnixTimestampFromDate($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, $updatedAt);
	}

	return array('items' => $result, 'updatedAt' => $maxUpdatedAt, 'totalCount' => count($rows));
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
	$totalCount = count($rows);

	$maxUpdatedAt = 0;
	$result = array();
	foreach ($rows as $row)
	{
		$entryId = $row['ENTRY_ID'];
		$result[$entryId] = '';
		$updatedAt = getUnixTimestampFromDate($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, $updatedAt);
	}
	
	$con = categoryEntryPeer::alternativeCon(null);
	$con->exec('SET SESSION group_concat_max_len = 1000000');

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

	return array('items' => $result, 'updatedAt' => $maxUpdatedAt, 'totalCount' => $totalCount);
}

function getUserGroupsUpdates($updatedAt)
{
	// get the puser+partner ids
	$c = new Criteria();
	$c->addSelectColumn(KuserKgroupPeer::KUSER_ID);
	$c->addSelectColumn(KuserKgroupPeer::PUSER_ID);
	$c->addSelectColumn(KuserKgroupPeer::PARTNER_ID);
	$c->addSelectColumn(KuserKgroupPeer::UPDATED_AT);
	$c->add(KuserKgroupPeer::UPDATED_AT, $updatedAt, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn(KuserKgroupPeer::UPDATED_AT);
	$c->setLimit(MAX_ITEMS);
	KuserKgroupPeer::setUseCriteriaFilter(false);
	$stmt = KuserKgroupPeer::doSelectStmt($c);
	KuserKgroupPeer::setUseCriteriaFilter(true);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$totalCount = count($rows);

	$maxUpdatedAt = 0;
	$kusers = array();
	$result = array();
	foreach ($rows as $row)
	{
		$kuserId = $row['KUSER_ID'];
		$kusers[$kuserId] = '';

		$puserId = $row['PUSER_ID'];
		$partnerId = $row['PARTNER_ID'];
		$key = md5($partnerId . '_' . strtolower($puserId));
		$result[$key] = '';

		$updatedAt = getUnixTimestampFromDate($row['UPDATED_AT']);
		$maxUpdatedAt = max($maxUpdatedAt, $updatedAt);
	}

	$con = KuserKgroupPeer::alternativeCon(null);
	$con->exec('SET SESSION group_concat_max_len = 1000000');

	// get the groups
	$groupIdsCol = 'GROUP_CONCAT('.KuserKgroupPeer::KGROUP_ID.')';
	$c = new Criteria();
	$c->addSelectColumn(KuserKgroupPeer::PARTNER_ID);
	$c->addSelectColumn(KuserKgroupPeer::PUSER_ID);
	$c->addSelectColumn($groupIdsCol);
	$c->addGroupByColumn(KuserKgroupPeer::KUSER_ID);
	$c->add(KuserKgroupPeer::KUSER_ID, array_keys($kusers), Criteria::IN);
	$c->add(KuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE);
	$stmt = KuserKgroupPeer::doSelectStmt($c);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// update user's groups (a user that wasn't fetched in the second query will remain empty)
	foreach ($rows as $row)
	{
		$puserId = $row['PUSER_ID'];
		$partnerId = $row['PARTNER_ID'];

		$key = md5($partnerId . '_' . strtolower($puserId));
		$groupsIds = $row[$groupIdsCol];
		$result[$key] = $groupsIds;
	}

	return array('items' => $result, 'updatedAt' => $maxUpdatedAt, 'totalCount' => $totalCount);
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

myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

$requestHandlers = array(
	'partner' => 'getPartnerUpdates',
	'user' => 'getUserUpdates',
	'entry' => 'getEntryUpdates',
	'categoryEntry' => 'getCategoryEntryUpdates',
	'userGroups' => 'getUserGroupsUpdates',
);

if (isset($requestHandlers[$requestType]))
{
	$result = call_user_func($requestHandlers[$requestType], $updatedAt);
	$limitReached = $result["totalCount"] == MAX_ITEMS;
	$result["limitReached"] = $limitReached;
}
else
{
	$result = array('error' => 'bad request');
}

echo json_encode($result);
die;
