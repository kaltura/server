<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 4)
{
	KalturaLog::info(' ---- Copy New Transcoding Profile From Partner 0 ---- ');
	die (' Error execute script, Usage: php copyNewTranscodingProfile.php
	 < transcodingId > 
	 < partnerId > 
	 < realrun / dryrun >' . PHP_EOL);
}

$transcodingProfileId = $argv[1];
$partnerId = $argv[2];

$transcodingProfiel = conversionProfile2Peer::retrieveByPKAndPartnerId($transcodingProfileId, 0);

$dryRun = true;
if (isset($argv[3]) && $argv[3] === 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

if ($transcodingProfiel && $transcodingProfiel->getStatus() == ConversionProfileStatus::ENABLED)
{
	KalturaLog::debug("Conversion profile id {$transcodingProfileId} found on partner 0");

	try
	{
		$new_profile = new conversionProfile2();
		$new_profile->setPartnerId($partnerId);
		$new_profile->setName($transcodingProfiel->getName());
		$new_profile->setSystemName($transcodingProfiel->getSystemName());
		$new_profile->setDescription($transcodingProfiel->getDescription());
		$new_profile->setConditionalProfiles($transcodingProfiel->getConditionalProfiles());
		$new_profile->setStatus(2);
		$new_profile->setType(1);
		$new_profile->save();
	}
	catch (Exception $e)
	{
		KalturaLog::debug("Could not creat the transcoding profile for Pid {$partnerId}");
		KalturaLog::debug($e->getMessage());
	}
}
else
{
	KalturaLog::debug("Conversion profile id {$transcodingProfileId} could not be found or is not enable on partner 0");
}
KalturaLog::info("Script Done!");