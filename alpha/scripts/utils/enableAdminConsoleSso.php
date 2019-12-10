<?php
if ($argc < 4)
{
	die ($argv[0]. " <redirect_url> <domain> <data_file_path> \n");
}

$redirectUrl = $argv[1];
$domain = $argv[2];
$dataFilePath = $argv[3];

require_once(__DIR__ . '/../bootstrap.php');
const ADMIN_CONSOLE_APPLICATION_TYPE = 'admin_console';

$partner = PartnerPeer::retrieveActiveByPK(Partner::ADMIN_CONSOLE_PARTNER_ID);
if($partner)
{
	allowSsoFeature($partner);
	addSSoProfile($domain, $redirectUrl, $dataFilePath);
}

function allowSsoFeature($partner)
{
	$partner->setUseSso(true);
	$partner->setBlockDirectLogin(true);
	$partner->save();
}

function addSsoProfile($domain, $redirectUrl, $dataFilePath)
{
	$ssoProfile = new Sso();
	$ssoProfile->setApplicationType(ADMIN_CONSOLE_APPLICATION_TYPE);
	$ssoProfile->setPartnerId(Partner::ADMIN_CONSOLE_PARTNER_ID);
	if($domain)
	{
		$ssoProfile->setDomain($domain);
	}

	if($dataFilePath && file_exists($dataFilePath))
	{
		$data = file_get_contents($dataFilePath);
		$ssoProfile->setData($data);
	}
	$ssoProfile->setRedirectUrl($redirectUrl);
	$ssoProfile->setStatus(SsoStatus::ACTIVE);
	$ssoProfile->save();
}

