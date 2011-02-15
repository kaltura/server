<?php
/**
 * @package deployment
 * @subpackage dragonfly.admin_roles_and_permissions
 * 
 * Set different reset password link to admin console partner
 * 
 * No need to re-run after server code deploy
 */

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');
define('ADMIN_CONSOLE_PARTNER_ID', -2);

$partner = PartnerPeer::retrieveByPK(ADMIN_CONSOLE_PARTNER_ID);
$partner->setPassResetUrlPrefixName('admin_console');

if ($dryRun)
{
	KalturaLog::log('DRY RUN! - Setting partner ['.$partner->getId().'] with password reset url prefix name of ['.$partner->getPassResetUrlPrefixName().']');
}
else
{
	KalturaLog::log('Setting partner ['.$partner->getId().'] with password reset url prefix name of ['.$partner->getPassResetUrlPrefixName().']');
	$partner->save();
}

//------------------------------------------------------

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;