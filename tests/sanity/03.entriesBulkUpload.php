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
$partnerId = $config['session']['partnerId'];
$adminSecretForSigning = $config['session']['adminSecret'];
$client->setKs($client->generateSessionV2($adminSecretForSigning, 'sanity-user', KalturaSessionType::USER, $partnerId, 86400, ''));
echo "Session started\n";





/**
 * Creates CSV file
 */
$csvPath = tempnam(sys_get_temp_dir(), 'csv');
$csvData = array(
	array(
		'*title' => 'bulk-sanity-test1',
		'description' => 'bulk-sanity-test1',
		'tags' => 'sanity,test1',
		'url' => $clientConfig->serviceUrl . 'content/templates/entry/data/kaltura_logo_animated_black.flv',
		'contentType' => 'video',
//		'conversionProfileId' => '',
//		'accessControlProfileId' => '',
		'category' => 'sanity>test',
//		'scheduleStartDate' => '',
//		'scheduleEndDate' => '',
//		'thumbnailUrl' => '',
//		'partnerData' => '',
//		'sshPrivateKey' => '',
//		'sshPublicKey' => '',
//		'sshKeyPassphrase' => '',
//		'entryId' => '',
//		'action' => '',
//		'ownerId' => '',
//		'entitledUsersEdit' => '',
//		'entitledUsersPublish' => '',
	),
	array(
		'*title' => 'bulk-sanity-test2',
		'description' => 'bulk-sanity-test2',
		'tags' => 'sanity,test2',
		'url' => $clientConfig->serviceUrl . 'content/templates/entry/data/kaltura_logo_animated_blue.flv',
		'contentType' => 'video',
//		'conversionProfileId' => '',
//		'accessControlProfileId' => '',
		'category' => 'sanity>test',
//		'scheduleStartDate' => '',
//		'scheduleEndDate' => '',
//		'thumbnailUrl' => '',
//		'partnerData' => '',
//		'sshPrivateKey' => '',
//		'sshPublicKey' => '',
//		'sshKeyPassphrase' => '',
//		'entryId' => '',
//		'action' => '',
//		'ownerId' => '',
//		'entitledUsersEdit' => '',
//		'entitledUsersPublish' => '',
	),
);

$f = fopen($csvPath, 'w');
fputcsv($f, array_keys(reset($csvData)));
foreach ($csvData as $csvLine)
	fputcsv($f, $csvLine);
fclose($f);

$bulkUpload = $client->media->bulkUploadAdd($csvPath);
/* @var $bulkUpload KalturaBulkUpload */
echo "Bulk upload added [$bulkUpload->id]\n";

$bulkUploadPlugin = KalturaBulkUploadClientPlugin::get($client);
while($bulkUpload)
{
	if($bulkUpload->status == KalturaBatchJobStatus::FINISHED || $bulkUpload->status == KalturaBatchJobStatus::FINISHED_PARTIALLY)
		break;

	if($bulkUpload->status == KalturaBatchJobStatus::FAILED)
	{
		echo "Bulk upload [$bulkUpload->id] failed\n";
		exit(-1);
	}
	if($bulkUpload->status == KalturaBatchJobStatus::ABORTED)
	{
		echo "Bulk upload [$bulkUpload->id] aborted\n";
		exit(-1);
	}
	if($bulkUpload->status == KalturaBatchJobStatus::FATAL)
	{
		echo "Bulk upload [$bulkUpload->id] failed fataly\n";
		exit(-1);
	}
	if($bulkUpload->status == KalturaBatchJobStatus::DONT_PROCESS)
	{
		echo "Bulk upload [$bulkUpload->id] removed temporarily from the batch queue \n";
	}

	sleep(15);
	$bulkUpload = $bulkUploadPlugin->bulk->get($bulkUpload->id);
}
if(!$bulkUpload)
{
	echo "Bulk upload not found\n";
	exit(-1);
}


/**
 * All is SABABA
 */
echo "OK\n";
exit(0);
