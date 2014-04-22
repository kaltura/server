<?php
$config = null;
$clientConfig = null;
/* @var $clientConfig KalturaConfiguration */
$client = null;
/* @var $client KalturaClient */

require_once __DIR__ . '/lib/init.php';
echo "Test started [" . __FILE__ . "]\n";


/**
 * Start a new session
 */
$adminSecretForSigning = $config['adminConsoleSession']['adminSecret'];
$client->setKs($client->generateSessionV2($adminSecretForSigning, null, KalturaSessionType::ADMIN, -2, 86400, ''));
echo "Admin console session started\n";


$partnerId = $config['session']['partnerId'];

/**
 * Delete the partner
 */
$systemPartnerClient = KalturaSystemPartnerClientPlugin::get($client);
$systemPartnerClient->systemPartner->updateStatus($partnerId, KalturaPartnerStatus::FULL_BLOCK);
echo "Partner [$partnerId] deleted\n";

/**
 * All is SABABA
 */
echo "OK\n";
exit(0);
