<?php
/**
 * @package deployment
 *
 * Match an image-entry's number of plays to its number of views
 *
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

$c = KalturaCriteria::create(entryPeer::OM_CLASS);
$c->add( entryPeer::MEDIA_TYPE, entry::ENTRY_MEDIA_TYPE_IMAGE );
$c->add( entryPeer::TYPE, entryType::MEDIA_CLIP);
$c->addAscendingOrderByColumn(entryPeer::CREATED_AT);
$c->addCondition("plays <> views");
$c->setLimit( 100 ); // Select in bulks of 100

$processing = true;
$lastCreatedAt = 0;
$processedEntries = array();
while( $processing )
{
	$criteriaForSelect = clone($c);
	if ( count($processedEntries) )
	{
		$criteriaForSelect->add(entryPeer::ID, $processedEntries, Criteria::NOT_IN);
	}

	$entries = entryPeer::doSelect( $criteriaForSelect );
	$processing = false;

	foreach($entries as $entry)
	{
		$entryId = $entry->getId();

		$entry->setPlays( $entry->getViews() );
		$entry->save();
		$processing = true;

		$createdAt = $entry->getCreatedAt( null );
		if ( $createdAt > $lastCreatedAt )
		{
			$lastCreatedAt = $createdAt;
			$processedEntries = array();
		}

		$processedEntries[] = $entryId;
	}

	kEventsManager::flushEvents();

	usleep( 50 * 1000 ); // Rest for 50 msec
	
	$c->add(entryPeer::CREATED_AT, $lastCreatedAt, Criteria::GREATER_EQUAL);
}
