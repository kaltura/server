<?php
$config = null;
$clientConfig = null;
/* @var $clientConfig KalturaConfiguration */
$client = null;
/* @var $client KalturaClient */

require_once __DIR__ . '/lib/init.php';



/**
 * Start a new session
 */
$partnerId = $config['session']['partnerId'];
$adminSecretForSigning = $config['session']['adminSecret'];
$client->setKs($client->generateSessionV2($adminSecretForSigning, 'sanity-user', KalturaSessionType::USER, $partnerId, 86400, ''));





/**
 * Creates CSV file
 */
$csvPath = tempnam(sys_get_temp_dir(), 'csv');
$csvData = array(
	array(
		'title' => '',
		'description' => '',
		'tags' => '',
		'url' => '',
		'contentType' => 'video',
//		'conversionProfileId' => '',
//		'accessControlProfileId' => '',
		'category' => '',
		'scheduleStartDate' => '',
		'scheduleEndDate' => '',
		'thumbnailUrl' => '',
		'partnerData' => '',
		'sshPrivateKey' => '',
		'sshPublicKey' => '',
		'sshKeyPassphrase' => '',
		'entryId' => '',
		'action' => '',
		'ownerId' => '',
		'entitledUsersEdit' => '',
		'entitledUsersPublish' => '',
	),
);





/**
 * All is SABABA
 */
exit(0);
