<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 3)
{
    die('Error execute script, Usage: php copyConversionProfile.php
	 < conversionProfileId >
	 < destPartnerId >
	 < realrun / dryrun >' . PHP_EOL);
}

$dryRun = true;
if (isset($argv[3]) && $argv[3] === 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$conversionProfileId = $argv[1];
$destPartnerId = $argv[2];

$conversionProfile = conversionProfile2Peer::retrieveByPKAndPartnerId($conversionProfileId, 0);

if (is_null($conversionProfile) || $conversionProfile->getStatus() == ConversionProfileStatus::DISABLED)
{
    die("Conversion profile id {$conversionProfileId} could not be found or is disabled on partner 0");
}

KalturaLog::debug("Conversion profile id {$conversionProfileId} found on partner 0");

try
{
    $newConversionProfile = copyConversionProfile($conversionProfile, $destPartnerId);

    try
    {
        $listOfFlavorParamsConversionProfile = $conversionProfile->getflavorParamsConversionProfiles();
        foreach ($listOfFlavorParamsConversionProfile as $flavorParamsConversionProfile)
        {
            /** @var $flavorParamsConversionProfile flavorParamsConversionProfile */
            $newFlavorParam = copyFlavorParam($flavorParamsConversionProfile, $newConversionProfile->getId());
        }
    }
    catch (Exception $exception)
    {
        KalturaLog::debug("Could not copy flavor params {$flavorParamsConversionProfile->getId()} from conversion profile {$conversionProfile->getId()}");
        KalturaLog::debug($exception->getMessage());
    }
}
catch (Exception $exception)
{
    KalturaLog::debug("Could not create the conversion profile for Pid {$destPartnerId}");
    KalturaLog::debug($exception->getMessage());
}

KalturaLog::info("Script Done!");

function copyConversionProfile($conversionProfile, $destPartnerId)
{
    $newConversionProfile = new conversionProfile2();
    $newConversionProfile->setPartnerId($destPartnerId);
    $newConversionProfile->setSystemName($conversionProfile->getSystemName());
    $newConversionProfile->setName($conversionProfile->getName());
    $newConversionProfile->setDescription($conversionProfile->getDescription());
    $newConversionProfile->setConditionalProfiles($conversionProfile->getConditionalProfiles());
    $newConversionProfile->setStatus($conversionProfile->getStatus());
    $newConversionProfile->setType($conversionProfile->getType());

    $newConversionProfile->save();
    return $newConversionProfile;
}

function copyFlavorParam($flavorParamsConversionProfile, $newConversionProfileId )
{
    /** @var $flavorParamsConversionProfile flavorParamsConversionProfile */
    $newFlavorParam = new flavorParamsConversionProfile();
    $newFlavorParam = $flavorParamsConversionProfile->copy();
    $newFlavorParam->setConversionProfileId($newConversionProfileId);
    $newFlavorParam->setCreatedAt(null);
    $newFlavorParam->setUpdatedAt(null);

    $newFlavorParam->save();
}