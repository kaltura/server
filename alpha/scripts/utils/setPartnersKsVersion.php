<?php

if ($argc < 2)
    die("Usage: php setPartnersKsVersion.php ksVersion <realrun | dryrun>"."\n");

require_once(__DIR__ . '/../bootstrap.php');

$dryRun = true;
if (in_array('realrun', $argv))
    $dryRun = false;

$ksVersion = $argv[1];

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$c = new Criteria();
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);

$partners = PartnerPeer::doSelect($c, $con);

while (count($partners))
{
    foreach ($partners as $partner)
    {
        KalturaLog::debug("Changing partnerID: [{$partner->getId()}] ks version to [$ksVersion]");
        $partner->setKSVersion($ksVersion);
        $partner->save();
    }

    kMemoryManager::clearMemory();

    $c = new Criteria();
    $c->addAscendingOrderByColumn(PartnerPeer::ID);
    $c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
    $c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
    $c->setLimit($countLimitEachLoop);
    $c->setOffset($offset);

    $partners = PartnerPeer::doSelect($c, $con);
    $offset +=  $countLimitEachLoop;
}

KalturaLog::debug("Done");