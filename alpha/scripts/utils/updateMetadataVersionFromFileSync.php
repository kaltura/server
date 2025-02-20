<?php
/**
 * Update the metadata version to match its latest fileSync version
 *
 *
 * Examples:
 * php updateMetadataVersionFromFileSync.php metadataId
 * php updateMetadataVersionFromFileSync.php metadataId realrun
 */

if ($argc < 2)
{
	die("Usage: php updateMetadataVersionFromFileSync.php metadataId [realrun]"."\n");
}

$metadataId = $argv[1];

$dryRun = true;
if (in_array('realrun', $argv))
{
	$dryRun = false;
}

//------------------------------------------------------


require_once(__DIR__ . '/../../../deployment/bootstrap.php');
kEventsManager::enableEvents(false);
$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$metadata = MetadataPeer::retrieveByPK($metadataId);
if (!$metadata)
{
	print("Metadata with id [$metadataId] not found\n");
	exit(1);
}

$c = new Criteria;
$c->add(FileSyncPeer::PARTNER_ID, $metadata->getPartnerId());
$c->add(FileSyncPeer::OBJECT_ID, $metadata->getId());
$c->add(FileSyncPeer::OBJECT_TYPE, 5);
$c->add(FileSyncPeer::OBJECT_SUB_TYPE, 1);
$c->add(FileSyncPeer::STATUS, 2);
$c->addDescendingOrderByColumn(FileSyncPeer::VERSION);

$fileSync = FileSyncPeer::doSelectOne($c);

if ($fileSync->getVersion() != $metadata->getVersion())
{
	print("Metadata with id [$metadataId] has version [{$metadata->getVersion()}], and its latest FileSync has version [{$fileSync->getVersion()}]\n");
}

$metadata->setVersion($fileSync->getVersion());
$metadata->save();
print("Done\n");
