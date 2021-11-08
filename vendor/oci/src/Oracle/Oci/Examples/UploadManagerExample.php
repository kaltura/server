<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use Oracle\Oci\ObjectStorage\Transfer\UploadManager;
use Oracle\Oci\ObjectStorage\Transfer\MultipartUploadException;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;

date_default_timezone_set('Europe/Istanbul');

echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;

$region = Region::getRegion("us-phoenix-1");
echo "Region: $region".PHP_EOL;

$auth_provider = new ConfigFileAuthProvider();

$c = new ObjectStorageAsyncClient(
    $auth_provider,
    $region
);

echo "----- getNamespace -----".PHP_EOL;
$response = $c->getNamespaceAsync()->wait();
$namespace = $response->getJson();

echo "Namespace = '{$namespace}'".PHP_EOL;

$bucket_name = "ENTER_YOUR_BUCKET_NAME_HERE";
$object_name = "ENTER_YOUR_OBJECT_NAME_HERE";
$filePath = "ENTER_YOUR_OBJECT_PATH_HERE";

$upload_manager = new UploadManager($c);

echo "prepare for uploading file to object storage".PHP_EOL;
$uploadPromise = $upload_manager->uploadFile($namespace, $bucket_name, $object_name, $filePath);

try {
    echo "wait for the promise to finish".PHP_EOL;
    $uploadRespone = $uploadPromise->wait();
    echo "Upload Success, response:".PHP_EOL;
    var_dump($uploadRespone);
} catch (MultipartUploadException $e) {
    $resumeInfo = $e->getMultipartResumeInfo();
    echo "Multipart Upload Failed, error detail: ".$e.PHP_EOL;
}
if (isset($resumeInfo)) {
    // ignoring error handling here
    echo "Retry upload using resumeUploadFile function".PHP_EOL;
    echo $resumeInfo;
    $resume_promise = $upload_manager->resumeUploadFileFromResumeInfo($resumeInfo);
    $resumeResponse = $resume_promise->wait();
    echo "Resume UploadSuccess, response:".PHP_EOL;
    var_dump($resumeResponse);
}

echo "Upload Manage Example End".PHP_EOL;
