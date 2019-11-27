<?php
if ($argc < 2)
{
	die ($argv[0]. " <redirect_url> <domain> \n");
}

$redirectUrl = $argv[1];
$domain = null;
if($argc == 3)
{
	$domain = $argv[2];
}

require_once(__DIR__ . '/../bootstrap.php');
const ADMIN_CONSOLE_APPLICATION_TYPE = 'admin_console';

$partner = PartnerPeer::retrieveActiveByPK(Partner::ADMIN_CONSOLE_PARTNER_ID);
if($partner)
{
	allowSsoFeature($partner);
	addSSoProfile($domain, $redirectUrl);
}

function allowSsoFeature($partner)
{
	$partner->setUseSso(true);
	$partner->setBlockDirectLogin(true);
	$partner->save();
}

function addSsoProfile($domain, $redirectUrl)
{
	$ssoProfile = new Sso();
	$ssoProfile->setApplicationType(ADMIN_CONSOLE_APPLICATION_TYPE);
	$ssoProfile->setPartnerId(Partner::ADMIN_CONSOLE_PARTNER_ID);
	$ssoProfile->setDomain($domain);
	$ssoProfile->setRedirectUrl($redirectUrl);
	$ssoProfile->setStatus(SsoStatus::ACTIVE);
	$ssoProfile->save();
}

