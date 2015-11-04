<?php
ini_set("memory_limit","1024M");

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$permCrit = new Criteria();
$permCrit->add(PermissionPeer::NAME, 'FEATURE_EDGE_SERVER');
$permCrit->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$permCrit->add(PermissionPeer::TYPE, PermissionType::SPECIAL_FEATURE);

$permissions = PermissionPeer::doSelect($permCrit);

foreach ($permissions as $perm)
{
	/* @var $perm Permission */
	$newPermission = $perm->copy();
	$newPermission->setName("FEATURE_SERVER_NODE");
	$newPermission->save();
}