<?php

chdir(dirname(__FILE__));
require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 2)
{
	die("Usage: $argv[0] <kuserId>");
}

$id = $argv[1];

$kuser = kuserPeer::retrieveByPKNoFilter($id);

if (!$kuser)
{
	die("Cannot find user with kuser Id [$id]");
}

$elasticManager = new kElasticSearchManager();
$sphinx = new kSphinxSearchManager();

$sphinx->saveToSphinx($kuser, true);
$elasticManager->saveToElastic($kuser);
