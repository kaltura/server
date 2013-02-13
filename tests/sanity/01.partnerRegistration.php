<?php

$config = null;
$client = null;
/* @var $client KalturaClient */

require_once __DIR__ . '/lib/init.php';

$partner = new KalturaPartner();
$partner->name = 'sanity-test';
$partner->website = 'sanity.test.com';
$partner->adminName = 'sanity-test';
$partner->adminEmail = 'sanity@test.com';
$partner->description = 'sanity-test';

$registeredPartner = $client->partner->register($partner);
/* @var $registeredPartner KalturaPartner */

if(!$registeredPartner || !$registeredPartner->id)
{
	echo "No partner created\n";
	exit(-1);
}

$config['session']['partnerId'] = $registeredPartner->id;
$config['session']['secret'] = $registeredPartner->secret;
$config['session']['adminSecret'] = $registeredPartner->adminSecret;

write_ini_file($config);
exit(0);