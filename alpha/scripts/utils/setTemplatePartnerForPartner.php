<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$dryrun = true;
$templatePartnerId = NULL;

if ($argc<2 || $argc>4)
{
    die ("usage: php  $argv[0] <PartnerID> [templatePartnerId] [dryrun/realrun] \n");
}

else if ($argc == 3)
{
    if(strtolower($argv[2]) == 'dryrun' || strtolower($argv[2]) == 'realrun')
    {
        $dryrun = strtolower($argv[2]) != 'realrun';
    }
    else if(is_numeric($argv[2]))
    {
        $templatePartnerId = (int) $argv[2];
    }
    else
    {
        die ("usage: php  $argv[0] <PartnerID> [templatePartnerId] [dryrun/realrun] \n");
    }
}

else if ($argc == 4)
{
    $templatePartnerId = (int) $argv[2];
    $dryrun = strtolower($argv[3]) != 'realrun';
}

KalturaStatement::setDryRun($dryrun);
KalturaLog::debug("dryrun value: [$dryrun]");

$partner_id = $argv[1];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}

if($templatePartnerId===NULL)
{
    $templatePartnerId = $partner->getId();
}
$partner->setTemplatePartnerId($templatePartnerId);
$partner->save();

KalturaLog::debug("Script setTemplatePartnerForPartner");
