<?php
/**
 * Usage:
 * php migrateAnnotationsDepthAndChildren.php [realrun/dryrun][id][partner id][limit]
 * 
 * Defaults are: dryrun, startUpdatedAt is zero, no limit
 * 
 * @package deploy
 * @subpackage update
 */

require_once(dirname(__FILE__).'/../../bootstrap.php');

$startUpdatedAt = null;
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
if(isset($argv[2]))
{
	$lastId = $argv[2];
}

$partnerId = null;
if(isset($argv[3]))
{
	$partnerId = $argv[3];
}

$limit = null;
if(isset($argv[4]))
{
	$limit = $argv[4];
}

$criteria = new Criteria();
$criteria->addAscendingOrderByColumn(EventNotificationTemplatePeer::ID);
if ($lastId)
	$criteria->add(EventNotificationTemplatePeer::ID, $lastId);
if ($partnerId)
	$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, $partnerId);
if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);
	
$results = EventNotificationTemplatePeer::doSelect($criteria);
$migrated = 0;
while (count($results) && (!$limit || $migrated < $limit))
{
	$migrated += count($results);
	foreach ($results as $result)
	{
		if ($result instanceof EmailNotificationTemplate)
		{
			if (is_array($result->getTo()))
			{
				$migrateTo = new kEmailNotificationStaticRecipientProvider();
				$migrateTo->setEmailRecipients($result->getTo());
				$result->setTo($migrateTo);
			}
			if (is_array($result->getCc()))
			{
				$migrateTo = new kEmailNotificationStaticRecipientProvider();
				$migrateTo->setEmailRecipients($result->getTo());
				$result->setTo($migrateTo);
			}
			if (is_array($result->getBcc()))
			{
				$migrateTo = new kEmailNotificationStaticRecipientProvider();
				$migrateTo->setEmailRecipients($result->getTo());
				$result->setTo($migrateTo);
			}
			if (is_array($result->getReplyTo()))
			{
				$migrateTo = new kEmailNotificationStaticRecipientProvider();
				$migrateTo->setEmailRecipients($result->getTo());
				$result->setTo($migrateTo);
			}
			
			$result->save();
			$lastId = $result->getId();
			KalturaLog::debug("Last handled ID: $lastId");
		}
	}
	
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->setOffset($migrated);
	$results = EventNotificationTemplatePeer::doSelect($nextCriteria);
	
}

KalturaLog::info("Done - migrated ". $migrated ." items");

