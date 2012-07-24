<?php

/**
 * set search index for a partner. 
 */
require_once '../bootstrap.php';


if ($argc !== 4) {
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . "[partner_id] [index type entry/category/kuser] [search_index_name]" . PHP_EOL );
}
$partnerId = $argv [1];
$searchIndex = $argv [2];
$searchIndexName = $argv [3];

$partner = PartnerPeer::retrieveByPK ( $partnerId );
if (! $partner)
	die ( "no such sub partner [$partnerId]." . PHP_EOL );

$searchIndexes = kConf::get('search_indexes');

if(!isset($searchIndexes[$searchIndex]))
	die ( "no such search index [$searchIndex]." . print_r($searchIndexes,true) . PHP_EOL );
	
$partner->setSearchIndex($searchIndex, $searchIndexName);
$partner->save();

echo "done." . PHP_EOL;


