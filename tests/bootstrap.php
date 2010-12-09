<?php

// load all required classes
require_once(dirname(__FILE__).'/../alpha/config/sfrootdir.php');
require_once(dirname(__FILE__).'/../api_v3/bootstrap.php');

// tests can include the following path if they need to use a Kalturaclient
DEFINE('KALTURA_CLIENT_PATH', dirname(__FILE__).'/../generator/output/php5full/KalturaClient.php');

// set DB
DbManager::setConfig(kConf::getDB());
DbManager::initialize();

