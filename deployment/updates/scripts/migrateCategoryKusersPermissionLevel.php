<?php
/**
 * Usage:
 * php migrateAnnotationsDepthAndChildren.php [realrun/dryrun] [id] [partnerId][limit]
 * 
 * Defaults are: dryrun, startUpdatedAt is zero, no limit
 * 
 * @package deploy
 * @subpackage update
 */

require_once(dirname(__FILE__).'/../../bootstrap.php');

$startUpdatedAt = null;
$limit = null;
$page = 500;

$dryRun = true;
if(isset($argv[1]) && strtolower($argv[1]) == 'realrun')
{
	$dryRun = false;
}
else 
{
	KalturaLog::info('Using dry run mode');
}
KalturaStatement::setDryRun($dryRun);

$lastId = null;
if (isset($argv[2]))
{
	$lastId = $argv[2];
}

$partnerId = null;
if(isset($argv[3]))
{
	$partnerId = $argv[3];
}

if(isset($argv[4]))
{
	$limit = $argv[4];
}

$criteria = new Criteria();
$criteria->add(categoryKuserPeer::PERMISSION_LEVEL, null, Criteria::NOT_EQUAL);
$criteria->addAscendingOrderByColumn(categoryKuserPeer::ID);
if ($partnerId)
	$criteria->add(categoryKuserPeer::PARTNER_ID, $partnerId);
if ($lastId)
	$criteria->add(categoryKuserPeer::ID, $lastId, Criteria::GREATER_THAN);

if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);
	
$results = categoryKuserPeer::doSelect($criteria);
$migrated = 0;
while (count($results) && (!$limit || $migrated < $limit))
{
	$migrated += count($results);
	foreach ($results as $result)
	{
		/* @var $result categoryKuser */
		switch ($result->getPermissionLevel())
		{
			case CategoryKuserPermissionLevel::MEMBER:
				$result->setPermissionNames(PermissionName::CATEGORY_VIEW.",".PermissionName::CATEGORY_SUBSCRIBE);
				break;
			case CategoryKuserPermissionLevel::CONTRIBUTOR:
				$result->setPermissionNames(PermissionName::CATEGORY_CONTRIBUTE.",".PermissionName::CATEGORY_VIEW.",".PermissionName::CATEGORY_SUBSCRIBE);
				break;
			case CategoryKuserPermissionLevel::MODERATOR:
				$result->setPermissionNames(PermissionName::CATEGORY_MODERATE.",".PermissionName::CATEGORY_VIEW.",".PermissionName::CATEGORY_SUBSCRIBE);
				break;
			case CategoryKuserPermissionLevel::MANAGER:
				$result->setPermissionNames(PermissionName::CATEGORY_EDIT.",".PermissionName::CATEGORY_CONTRIBUTE.",".PermissionName::CATEGORY_MODERATE.",".PermissionName::CATEGORY_VIEW.",".PermissionName::CATEGORY_SUBSCRIBE);
				break;
			case CategoryKuserPermissionLevel::NONE:
				$result->setPermissionNames(PermissionName::CATEGORY_SUBSCRIBE);
				break;
		}
		
		$result->save();
		KalturaLog::info("Last handled categoryKuser id: [" . $result->getId() ."]");
	}
	
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->setOffset($migrated);
	$results = categoryKuserPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done - migrated ". count($results) ." items");