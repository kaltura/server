<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';
require "ObjectStorageExampleInclude.php";

use DateTime;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;
use Oracle\Oci\Common\Logging\EchoLogAdapter;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\OciBadResponseException;

// TODO: Update these to your own values
$bucket_name = "mricken-test";
$file_to_upload = "composer.json";
$compartmentId = "ocid1.compartment.oc1..aaaaaaaagc6xvyuhplu3mkb4ewmgjma6uuxfwz56d3gk6alpsc5bfj54wwna";
// END TODO: Update these to your own values

date_default_timezone_set('Etc/UTC');
Logger::setGlobalLogAdapter(new EchoLogAdapter(0, [
    // "Oracle\\Oci\\ObjectStorage\\ObjectStorageClient\\middleware\\uri" => LOG_DEBUG,
    // "Oracle\\Oci\\Common\\OciResponse" => LOG_DEBUG
]));

echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;

$auth_provider = new ConfigFileAuthProvider();
echo "Region from config file: {$auth_provider->getRegion()}" . PHP_EOL;

// $auth_provider = new ConfigFileAuthProvider(ConfigFile::loadFromFile(ConfigFile::getUserHome() . "/.oci/config_us-phoenix-1", "OTHER"));

$c = new ObjectStorageClient($auth_provider);
runObjectStorageExample($c, $auth_provider);
