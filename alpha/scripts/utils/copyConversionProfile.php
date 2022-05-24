<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 3)
{
	die (' Error execute script, Usage: php copyConversionProfile.php
	 < conversionProfileId >
	 < partnerId >
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
$partnerId = $argv[2];

$conversionProfile = conversionProfile2Peer::retrieveByPKAndPartnerId($conversionProfileId, 0);

if (!is_null($conversionProfile) && $conversionProfile->getStatus() == ConversionProfileStatus::ENABLED)
{
	KalturaLog::debug("Conversion profile id {$conversionProfileId} found on partner 0");

	try
	{
		$newConversionProfile = new conversionProfile2();
		$newConversionProfile->setPartnerId($partnerId);
		$newConversionProfile->setSystemName($conversionProfile->getSystemName());
		$newConversionProfile->setName($conversionProfile->getName());
		$newConversionProfile->setDescription($conversionProfile->getDescription());
		$newConversionProfile->setConditionalProfiles($conversionProfile->getConditionalProfiles());
		$newConversionProfile->setStatus($conversionProfile->getStatus());
		$newConversionProfile->setType($conversionProfile->getType());

		$newConversionProfile->save();

		try
		{
			$listOfFlavorParamsConversionProfile = $conversionProfile->getflavorParamsConversionProfiles();
			foreach ($listOfFlavorParamsConversionProfile as $flavorParamsConversionProfile)
			{
				/** @var $flavorParamsConversionProfile flavorParamsConversionProfile */

				$newFlavorParam = new flavorParamsConversionProfile();
				$newFlavorParam = $flavorParamsConversionProfile->copy();
				$newFlavorParam->setConversionProfileId($newConversionProfile->getId());
				$newFlavorParam->setCreatedAt(null);
				$newFlavorParam->setUpdatedAt(null);

				$newFlavorParam->save();
			}
		}
		catch (Exception $exception)
		{
			KalturaLog::debug("Count not copy flavor params from conversion profile {$conversionProfile->getId()}");
			KalturaLog::debug($exception->getMessage());
		}
	}
	catch (Exception $exception)
	{
		KalturaLog::debug("Could not create the conversion profile for Pid {$partnerId}");
		KalturaLog::debug($exception->getMessage());
	}
}
else
{
	KalturaLog::debug("Conversion profile id {$conversionProfileId} could not be found or is not enable on partner 0");
}
KalturaLog::info("Script Done!");
