<?php
function usage()
{
    die("\nUsage - clear admin user data: php clearAdminUserData.php <PartnerId> <partnerAdminEmail> <Are you sure : yes/no>\n\n".
        "FYI - Following this api the user and its login-data will be deleted without any trace.\n\n");
}
if($argc != 4 || $argv[3] !== "yes")
{
    return usage();
}

require("/opt/kaltura/app/alpha/scripts/bootstrap.php");

$partnerId = $argv[1];
$partnerAdminEmail = $argv[2];

//find userLoginData by login email and validate partner ID
$partner = PartnerPeer::retrieveByPK($partnerId);
if(!$partner || $partner->getAdminEmail()!==$partnerAdminEmail)
{
    die("\nWrong admin email supplied, expected {$partner->getAdminEmail()} found {$partnerAdminEmail}\n\n");
}

//get all admin users for the account
$adminUsers = Partner::getAdminLoginUsersList($partnerId);
foreach($adminUsers as $adminUser)
{
    $loginData = $adminUser->getLoginData();

    //Delete the kuser
    KalturaLog::log("{$argv[0]} Deleting kuser, puserId: {$adminUser->getPuserId()} id: {$adminUser->getId()}");
    $adminUser->delete();

    //Delete the login data
    KalturaLog::log("{$argv[0]} Deleting login data, id: {$loginData->getId()} login email: {$loginData->getLoginEmail()} ");
    $loginData->delete();
}

KalturaLog::log("{$argv[0]} Done removing all admin users from account: {$partnerId}");