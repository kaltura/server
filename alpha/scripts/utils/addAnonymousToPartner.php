<?php
require_once(__DIR__ . '/../bootstrap.php');

const USER_ZERO_SCREEN_NAME = 'Unknown';

function addUser($partnerId, $userName)
{
    echo "Adding user:{$userName} to {$partnerId}";
    return kuserPeer::createUniqueKuserForPartner($partnerId, $userName);
}

if($argc < 2){
    echo "Usage: php $argv[0] [partner id]" . PHP_EOL;
    die("Not enough parameters" . "\n");
}

$partnerId = $argv[1];
$user = addUser($partnerId, '0');
if($user->getScreenName() != USER_ZERO_SCREEN_NAME);
{
    $user->setScreenName(USER_ZERO_SCREEN_NAME);
    $user->save();
}

addUser($partnerId, '');