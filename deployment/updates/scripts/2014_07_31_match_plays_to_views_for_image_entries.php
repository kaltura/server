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
$c->addAscendingOrderByColumn(entryPeer::INT_ID);
$c->addCondition("plays <> views");
$c->setLimit( 100 ); // Select in bulks of 100

$lastIntId = 0;
$entries = entryPeer::doSelect($c);
while(count($entries))
{
	foreach($entries as $entry)
	{
		$entry->setPlays( $entry->getViews() );
		$entry->save();
		$lastIntId = $entry->getIntId();
	}

	usleep( 50 * 1000 ); // Rest for 50 msec
	
	$c->add(entryPeer::INT_ID, $lastIntId, Criteria::GREATER_THAN);
	$entries = entryPeer::doSelect( $c );
}
