<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');

if ($argc < 5)
{
	die('Error executing script, Usage: php updateConversionprofileSystemName.php.php
	 < conversionProfileId >
	 < newSystemName >
	 < partnerId >
	 < realrun / dryrun >' . PHP_EOL);
}

$conversionProfileId = $argv[1];
$newSystemName = $argv[2];
$partnerId = $argv[3];

$dryRun = true;
if ($argc == 5 && $argv[4] === 'realrun')
{
	$dryRun = false;
}


KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');
KalturaLog::info("Partner ID [$partnerId]");
KalturaLog::info("Attempting to update conversion profile ID [$conversionProfileId] with new system name [$newSystemName]");

$conversionProfile = conversionProfile2Peer::retrieveByPKAndPartnerId($conversionProfileId, $partnerId);

if (!$conversionProfile)
{
	KalturaLog::err("Conversion profile ID [$conversionProfileId] not found for partner ID [$partnerId]");
	die('ERROR: Conversion profile not found' . PHP_EOL);
}

if ($conversionProfile->getSystemName() === $newSystemName)
{
	KalturaLog::err("Conversion profile ID [$conversionProfileId] already has system name [$newSystemName]");
	die('ERROR: Conversion profile already has the same system name' . PHP_EOL);
}

KalturaLog::info("Found conversion profile: ID [{$conversionProfile->getId()}], Current system name [{$conversionProfile->getSystemName()}], Partner ID [{$conversionProfile->getPartnerId()}]");

$c = KalturaCriteria::create(conversionProfile2Peer::OM_CLASS);
$c->add(conversionProfile2Peer::PARTNER_ID, $partnerId, Criteria::EQUAL);
$c->add(conversionProfile2Peer::SYSTEM_NAME, $newSystemName, Criteria::EQUAL);
$c->add(conversionProfile2Peer::ID, $conversionProfileId, Criteria::NOT_EQUAL);

if (conversionProfile2Peer::doCount($c))
{
	KalturaLog::err("System name [$newSystemName] already exists");
	die('ERROR: System name already exists' . PHP_EOL);
}

$oldSystemName = $conversionProfile->getSystemName();
$conversionProfile->setSystemName($newSystemName);
$conversionProfile->save();

KalturaLog::info("Successfully updated conversion profile ID [$conversionProfileId] system name from [$oldSystemName] to [$newSystemName]");
echo "SUCCESS: Conversion profile ID [$conversionProfileId] system name updated from [$oldSystemName] to [$newSystemName]" . PHP_EOL;
