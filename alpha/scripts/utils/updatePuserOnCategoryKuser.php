<?php

require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
if ($argc < 3)
	die("Usage: php $argv[0] partnerId kusersFilePath <realrun | dryrun>"."\n");

$partnerId = $argv[1] ;
$kusersPath = $argv[2];
$dryrun = true;
if($argc == 4 && $argv[3] == 'realrun')
	$dryrun = false;
KalturaStatement::setDryRun($dryrun);
KalturaLog::debug('dryrun value: ['.$dryrun.']');
$kusers = file ($kusersPath) or die ('Could not read file'."\n");

foreach ($kusers as $kuserId)
{
	$kuserId = trim($kuserId);
	$puserIdFromKuserTable = getPuserIdFromKuserTable ($partnerId, $kuserId);
	if($puserIdFromKuserTable)
	{
		$categoryList = getCategoryListByKuser($partnerId, $kuserId);
		foreach ($categoryList as $categoryKuserItem)
		{
			$puserIdFromCategoryKuser = $categoryKuserItem->getPuserId();
			if (strcmp(trim($puserIdFromKuserTable),trim($puserIdFromCategoryKuser)))
			{
				KalturaLog::debug('kuserId ['.$kuserId.'] : update puser_id on categoryKuser table from ['.$puserIdFromCategoryKuser.'] to ['.$puserIdFromKuserTable.']');
				kCurrentContext::$partner_id = $partnerId;
				$categoryKuserItem->setPuserId($puserIdFromKuserTable);
				$categoryKuserItem->save();
			}
		}
	}
}

function getPuserIdFromKuserTable($partnerId, $kuserId)
{
	$Critiria = new Criteria();
	$Critiria->add(kuserPeer::PARTNER_ID, $partnerId);
	$Critiria->add(kuserPeer::ID, $kuserId);
	$kuser = kuserPeer::doSelectOne($Critiria);
	return $kuser->getPuserId();
}

function getCategoryListByKuser($partnerId, $kuserId)
{
	$Critiria = new Criteria();
	$Critiria->add(categoryKuserPeer::PARTNER_ID, $partnerId);
	$Critiria->add(categoryKuserPeer::KUSER_ID, $kuserId);
	return categoryKuserPeer::doSelect($Critiria);
}
