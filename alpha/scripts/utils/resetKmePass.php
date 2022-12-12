<?php
require_once(__DIR__ . '/../bootstrap.php');

if($argc < 4)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php {$argv[0]} <new password> <minPartnerId> <maxPartnerId> [realrun / dryrun]\n";
	exit;
}

$dryRun = true;
if ($argc == 5 && $argv[4] == 'realrun')
{
	$dryRun = false;
}

$newPass = $argv[1];
$minPartnerId = $argv[2];
$maxPartnerId = $argv[3];

const CHUNK_SIZE = 500;

const LOG_DIR = '/tmp/kme-reset-pass/';

$successfulPids = array();
$failedPids = array();

$currentPartnerId = $minPartnerId;

do {
	$c = new Criteria();
	$c->add(PartnerPeer::ID, $currentPartnerId, Criteria::GREATER_THAN);
	$c->addAnd(PartnerPeer::ID, $maxPartnerId, Criteria::LESS_THAN);
	$c->addAnd(PartnerPeer::STATUS, 1);
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->setLimit(CHUNK_SIZE);
	$partners = PartnerPeer::doSelect($c);

	KalturaLog::info("partners count is " . count($partners));

	foreach($partners as $partner)
	{
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
		if (!$loginData)
		{
			$failedPids[] = $partner->getId();
			continue;
		}
		if (!$dryRun) {
			$loginData->resetPassword($newPass);
		}
		$successfulPids[] = $partner->getId();
	}

} while (count($partners));

$currentTime = time();

if (!file_exists(LOG_DIR)) {
	mkdir(LOG_DIR, 0777, true);
}

$successfulPidsFileName = LOG_DIR . "/reset-pass-success-$currentTime.txt";
$failedPidsFileName = LOG_DIR . "/reset-pass-failed-$currentTime.txt";

file_put_contents($successfulPidsFileName, implode("\n", $successfulPids));
file_put_contents($failedPidsFileName, implode("\n", $failedPids));

KalturaLog::info("\nResults:\nsucceeded: " . count($successfulPids) . "\nfailed: " . count($failedPids));
KalturaLog::info("Results written to $successfulPidsFileName , $failedPidsFileName");
