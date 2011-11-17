<?php

/**
 * add sub partner to existing partner group (as adding it to the master partner's group). 
 */

ini_set ( "memory_limit", "256M" );

define ( 'SF_ROOT_DIR', realpath ( dirname ( __FILE__ ) . '/../../alpha/' ) );
define ( 'SF_APP', 'kaltura' );
define ( 'SF_ENVIRONMENT', 'batch' );
define ( 'SF_DEBUG', true );

require_once (SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
require_once (SF_ROOT_DIR . '/../infra/bootstrap_base.php');
require_once (KALTURA_INFRA_PATH . DIRECTORY_SEPARATOR . "KAutoloader.php");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::setClassMapFilePath ( './logs/classMap.cache' );
KAutoloader::register ();

error_reporting ( E_ALL );

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

if ($argc !== 3) {
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [sub partner] [master partner]" . PHP_EOL );
}
$subPartnerId = $argv [1];
$masterPartnerId = $argv [2];

$subPartner = PartnerPeer::retrieveByPK ( $subPartnerId );
if (! $subPartner)
	die ( "no such sub partner [$subPartner]." . PHP_EOL );
$masterPartner = PartnerPeer::retrieveByPK ( $masterPartnerId );
if (! $masterPartner)
	die ( "no such master partner [$subPartner]." . PHP_EOL );

PermissionPeer::clearInstancePool ();

$c = new Criteria ();
$c->addAnd ( PermissionPeer::PARTNER_ID, $masterPartner->getId(), Criteria::EQUAL );
$c->addAnd ( PermissionPeer::TYPE, PermissionType::PARTNER_GROUP, Criteria::EQUAL );
$c->addAnd ( PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
$permission = PermissionPeer::doSelectOne ( $c );

if (! $permission)
	die ("Master partner group doesnot exists" . PHP_EOL);

$group = $permission->getPartnerGroup();
$groupArr = explode(',', $group);
$groupArr[] = $subPartner->getId();
$newGroup = implode(',', $groupArr);

$permission->setPartnerGroup($newGroup);
$permission->save();

echo "current partner group [$newGroup] " . PHP_EOL;
echo "done." . PHP_EOL;


