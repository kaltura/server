<?php
/**
 * @package deployment
 * @subpackage live.liveParams
 *
 * Add live conversion profile to existing partners
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

function updateBroadCastUrl(LiveStreamEntry $entry)
{
	$entry->setPrimaryBroadcastingUrl('');
	$entry->setSecondaryBroadcastingUrl('');
			
	$entryDc = substr($entry->getId(), 0, 1);

	$broadcastUrlManager = kBroadcastUrlManager::getInstance($entry->getPartnerId());
	
	$dcs = kDataCenterMgr::getAllDcs(true);
	foreach($dcs as $dc)
	{
		if($dc == $entryDc)
		{
			$entry->setPrimaryBroadcastingUrl($broadcastUrlManager->getBroadcastUrl($entry, $dc, kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX));
		}
		else 
		{
			$entry->setSecondaryBroadcastingUrl($broadcastUrlManager->getBroadcastUrl($entry, $dc, kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX));
		}
	}
	
	$entry->save();
}

$c = new Criteria();
$c->add(entryPeer::STATUS, entryStatus::READY);
$c->add(entryPeer::TYPE, entryType::LIVE_STREAM);
$c->add(entryPeer::SOURCE, EntrySourceType::LIVE_STREAM);
$c->addAscendingOrderByColumn(entryPeer::INT_ID);
$c->setLimit(100);

$offset = 0;
$entries = entryPeer::doSelect($c);
while(count($entries))
{
	foreach($entries as $entry)
		updateBroadCastUrl($entry);
	
	$offset += count($entries);
	$c->setOffset($offset);
	$entries = entryPeer::doSelect($c);
}
