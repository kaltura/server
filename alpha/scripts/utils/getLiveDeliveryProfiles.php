<?php
require_once(__DIR__ . '/../bootstrap.php');

$output_csv_file = '/output.csv';
$output_handle = fopen($output_csv_file, 'w');
fputcsv($output_handle, ['PID', 'Live Delivery Profiles']);

$partners_exists = true;
$bulk_size = 500;
$lowest_partner_id = 100;
$count_pid = 0;
while($partners_exists)
{
    $c = new Criteria();
    $c->addAnd(PartnerPeer::STATUS, Partner::PARTNER_STATUS_DELETED, Criteria::NOT_EQUAL);
    $c->addAnd(PartnerPeer::ID, $lowest_partner_id, Criteria::GREATER_THAN);
    $c->addAscendingOrderByColumn(PartnerPeer::ID);
    $c->setLimit($bulk_size);
    $partners = PartnerPeer::doSelect($c);
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
            $count_pid++;
            $pid = $partner->getId();
            KalturaLog::debug("$count_pid Currently processing PID $pid");
            $live_delivery_profile_ids = $partner->getFromCustomData('live_delivery_profile_ids');
            $live_delivery_profile_ids_string = implode(',', $live_delivery_profile_ids);
            fputcsv($output_handle, [$pid, $live_delivery_profile_ids_string]);
        }
    }
    $partner = end($partners);
    if($partner)
    {
        $lowest_partner_id = $partner->getId();
    }
    unset($partners);
    PartnerPeer::clearInstancePool();
}
