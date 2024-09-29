<?php
require_once(__DIR__ . '/../bootstrap.php');
$filePath = 'data.txt';
$ACPdata = [];
$handle = fopen($filePath, 'r');
if ($handle)
{
    while (($line = fgets($handle)) !== false)
    {
        list($pid, $details) = explode(' ', $line, 2);
        $ACPdata[trim($pid)] = trim($details);
    }
    fclose($handle);
}

$outputCsvFile = '/output.csv';
$outputHandle = fopen($outputCsvFile, 'w');
fputcsv($outputHandle, ['PID', 'Live Delivery Profile', 'ACP Delivery Profiles']);

$partners_exists = true;
$bulk_size = 500;
$highest_partner_id = 100;
$countPID = 0;
$countQueries= 0;
while($partners_exists)
{
    $c = new Criteria();
    $c->addAnd(PartnerPeer::STATUS, Partner::PARTNER_STATUS_DELETED, Criteria::NOT_EQUAL);
    $c->addAnd(PartnerPeer::ID, $highest_partner_id, Criteria::GREATER_THAN);
    $c->addAscendingOrderByColumn(PartnerPeer::ID);
    $c->setLimit($bulk_size);
    $partners = PartnerPeer::doSelect($c);
    $countQueries++;
    if (!$partners)
    {
        KalturaLog::debug( "No more partners." );
        $partners_exists = false;
    }
    else
    {
        KalturaLog::debug( "Looping ". count($partners) ." partners" );
        foreach($partners as $partner)
        {
            $countPID++;
            $pid = $partner->getId();
            KalturaLog::debug("$countPID Currently processing PID $pid");
            $acpDeliveryProfileData = null;
            $liveDeliveryProfileIds = serialize($partner->getFromCustomData('live_delivery_profile_ids'));
            var_dump($liveDeliveryProfileIds);
            if (array_key_exists($pid, $ACPdata))
            {
                $acpDeliveryProfileData = $ACPdata[$pid]; // Set to the value if it exists
            }
            fputcsv($outputHandle, [$pid, $liveDeliveryProfileIds, $acpDeliveryProfileData]);
        }
    }
    $partner = end($partners);
    if($partner)
    {
        $highest_partner_id = $partner->getId();
    }
    unset($partners);
    PartnerPeer::clearInstancePool();

    if ($countQueries % 10 === 0)
    {
        KalturaLog::debug('Sleeping for 1 second');
        sleep(1);
    }
}
