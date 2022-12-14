<?php
require_once(__DIR__ . '/../bootstrap.php');

if($argc < 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php {$argv[0]} <new_password> <partner_ids_file_path> [realrun / dryrun]\n";
	exit;
}

$dryRun = true;
if ($argc == 4 && $argv[3] == 'realrun')
{
	$dryRun = false;
}

$newPass = $argv[1];
$pidsFilePath = $argv[2];

const CHUNK_SIZE = 500;

const LOG_DIR = '/tmp/kme-reset-pass/';

$pidsList = file_get_contents($pidsFilePath);
$pidsList = array_filter(explode("\n", $pidsList));

$successfulPids = array();
$failedPids = array();

$pidsChunks = array_chunk($pidsList, CHUNK_SIZE);

foreach ($pidsChunks as $pidsChunk)
{
	$c = new Criteria();
	$c->add(PartnerPeer::ID, $pidsChunk, Criteria::IN);
	$c->addAnd(PartnerPeer::STATUS, 1);
	$partners = PartnerPeer::doSelect($c);

	KalturaLog::info("partners count is " . count($partners));

	foreach ($partners as $partner) {
		$currentPartnerId = $partner->getId();
		$additionalParams = $partner->getAdditionalParams();
		if (!isset($additionalParams['note']) || $additionalParams['note'] !== 'Created by KME script') {
			KalturaLog::info("skipping partner " . $partner->getId() . " as it wasn't created by KME script");
			continue;
		}
		KalturaLog::info("resetting password for partner " . $partner->getId());
		$loginDataCriteria = new Criteria();
		$loginDataCriteria->add(UserLoginDataPeer::LOGIN_EMAIL, $partner->getAdminEmail());
		$loginDataCriteria->add(UserLoginDataPeer::CONFIG_PARTNER_ID, $partner->getId());
		$loginData = UserLoginDataPeer::doSelectOne($loginDataCriteria);
		if (!$loginData) {
			$failedPids[] = $partner->getId();
			continue;
		}
		if (!$dryRun) {
			$loginData->resetPassword($newPass);
		}
		$successfulPids[] = $partner->getId();
	}
}

$currentTime = time();

if (!file_exists(LOG_DIR))
{
	mkdir(LOG_DIR, 0777, true);
}

$successfulPidsFileName = LOG_DIR . "/reset-pass-success-$currentTime.txt";
$failedPidsFileName = LOG_DIR . "/reset-pass-failed-$currentTime.txt";

file_put_contents($successfulPidsFileName, implode("\n", $successfulPids));
file_put_contents($failedPidsFileName, implode("\n", $failedPids));

KalturaLog::info("\nResults:\nsucceeded: " . count($successfulPids) . "\nfailed: " . count($failedPids));
KalturaLog::info("Results written to $successfulPidsFileName , $failedPidsFileName");
