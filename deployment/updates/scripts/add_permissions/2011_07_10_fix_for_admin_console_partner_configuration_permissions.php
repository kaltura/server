<?php

require_once(dirname(__FILE__).'/../../../bootstrap.php');

PermissionPeer::clearInstancePool();

$permissionsToChange = array(
    'systemPartner.'.SystemPartnerPermissionName::SYSTEM_ADMIN_PUBLISHER_CONFIG_ACCOUNT_INFO,
    'systemPartner.'.SystemPartnerPermissionName::SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS,
    'systemPartner.'.SystemPartnerPermissionName::SYSTEM_ADMIN_PUBLISHER_CONFIG_TECH_DATA,
);

$c = new Criteria();
$c->addAnd(PermissionPeer::NAME, $permissionsToChange, Criteria::IN);
$c->addAnd(PermissionPeer::PARTNER_ID, Partner::ADMIN_CONSOLE_PARTNER_ID, Criteria::EQUAL);

$permissions = PermissionPeer::doSelect($c);

if (count($permissions) != count($permissionsToChange))
{
    KalturaLog::err('ERROR - did not get all required permissions!');
    die('ERROR');
}

foreach ($permissions as $permission)
{
    $permission->setDependsOnPermissionNames('');
    $permission->save();
    KalturaLog::log('Updated permission id ['.$permission->getId().'] name ['.$permission->getName().']');
}

KalturaLog::log('Done');



