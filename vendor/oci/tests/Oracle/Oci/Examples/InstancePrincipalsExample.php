<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';
require "ObjectStorageExampleInclude.php";

use GuzzleHttp\Exception\ServerException;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\Common\Auth\InstancePrincipalsAuthProvider;
use Oracle\Oci\Common\Logging\EchoLogAdapter;
use Oracle\Oci\Common\Logging\Logger;

// This example must be run on an OCI instance.
// 1. Create a dynamic group. You can use a matching rule like this to get all instances in a certain compartment:
//        Any {instance.compartment.id = '<ocid-of-compartment>'}
// 2. Start an OCI instance. Make sure that it is matched by the dynamic group, e.g. by creating it in the correct compartment.
// 3. Create a policy for the dynamic group that grants the desired permissions. For example:
//        Allow dynamic-group <name-of-dynamic-group> to manage buckets in compartment <name-of-compartment>
//        Allow dynamic-group <name-of-dynamic-group> to manage objects in compartment <name-of-compartment>
//        Allow dynamic-group mricken-test-dg to manage objectstorage-namespaces in compartment mricken-test
// 4. Copy the OCI PHP SDK and this example to the OCI instance (using scp or rsync).
// 5. SSH into the OCI instance and run it.

// TODO: Update these to your own values
$bucket_name = "mricken-test";
$file_to_upload = "composer.json";
$compartmentId = "ocid1.compartment.oc1..aaaaaaaagc6xvyuhplu3mkb4ewmgjma6uuxfwz56d3gk6alpsc5bfj54wwna";
// END TODO: Update these to your own values

date_default_timezone_set('Etc/UTC');

Logger::setGlobalLogAdapter(new EchoLogAdapter(0, [
    "Oracle\\Oci\\Common\\OciResponse" => LOG_DEBUG,
    "Oracle\\Oci\\Common\\Auth" => LOG_DEBUG
]));

try {
    $auth_provider = new InstancePrincipalsAuthProvider();
} catch (ServerException $e) {
    if (strpos($e, "cannotconnect") !== false) {
        echo("This sample only works when running on an OCI instance. Are you sure you’re running on an OCI instance?" . PHP_EOL
        . "For more info see: https://docs.cloud.oracle.com/Content/Identity/Tasks/callingservicesfrominstances.htm" . PHP_EOL);
        exit(1);
    }
    throw $e;
}

$c = new ObjectStorageClient($auth_provider);
runObjectStorageExample($c, $auth_provider);
