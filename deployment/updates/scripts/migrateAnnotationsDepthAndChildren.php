<?php
/**
 * Usage:
 * php migrateAnnotationsDepthAndChildren.php [realrun/dryrun] [startUpdatedAt] [limit]
 * 
 * Defaults are: dryrun, startUpdatedAt is zero, no limit
 * 
 * @package deploy
 * @subpackage update
 */

require_once(dirname(__FILE__).'/../../bootstrap.php');

$startUpdatedAt = null;
$limit = null;
$page = 200;

$dryRun = true;
if(isset($argv[1]) && strtolower($argv[1]) == 'realrun')
{
	$dryRun = true;
}
else 
{
	KalturaLog::info('Using dry run mode');
}
KalturaStatement::setDryRun($dryRun);
	
if(isset($argv[2]))
	$startUpdatedAt = $argv[2];
	
if(isset($argv[3]))
	$limit = $argv[3];
	
$criteria = new Criteria();
$criteria->add(CuePointPeer::TYPE, AnnotationPlugin::getCuePointTypeCoreValue(AnnotationCuePointType::ANNOTATION));
if($startUpdatedAt)
	$criteria->add(CuePointPeer::UPDATED_AT, $startUpdatedAt, Criteria::GREATER_THAN);
$criteria->addAscendingOrderByColumn(CuePointPeer::UPDATED_AT);
$criteria->addAscendingOrderByColumn(CuePointPeer::INT_ID);

if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);

$annotations = CuePointPeer::doSelect($criteria);
$migrated = 0;
while(count($annotations) && (!$limit || $migrated < $limit))
{
	KalturaLog::info("Migrating [" . count($annotations) . "] partners.");
	$migrated += count($annotations);
	foreach($annotations as $annotation)
	{
		/* @var $annotation Annotation */

		$annotation->setDepth($annotation->getDepth());
		$annotation->setChildrenCount($annotation->getChildrenCount());
		$annotation->setDirectChildrenCount($annotation->getDirectChildrenCount());
		if($annotation->getParentId())
			$annotation->putInCustomData(Annotation::CUSTOM_DATA_FIELD_ROOT_PARENT_ID, $annotation->getRootParentId());
		
		$startUpdatedAt = $annotation->getUpdatedAt(null);
		KalturaLog::info("Migrated annotation [" . $annotation->getId() . "] with updated at [$startUpdatedAt: " . $annotation->getUpdatedAt() . "].");
	}
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->add(CuePointPeer::UPDATED_AT, $startUpdatedAt, Criteria::GREATER_THAN);
	$annotations = CuePointPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done - migrated $migrated items");
