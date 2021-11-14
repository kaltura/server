<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use Oracle\Oci\ObjectStorage\Transfer\UploadManager;
use Oracle\Oci\ObjectStorage\Transfer\MultipartUploadException;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;
use Oracle\Oci\ObjectStorage\Transfer\UploadManagerRequest;
use UploadManagerConstants;

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
$upload_manager = new UploadManager($c);

// Example for uploading with file
$filePath = "ENTER_YOUR_OBJECT_PATH_HERE";
$fileName = "ENTER_YOUR_OBJECT_NAME_HERE";

echo "prepare for uploading file to object storage".PHP_EOL;
$uploadPromiseForFile = $upload_manager->upload(UploadManagerRequest::createUploadManagerRequest(
    $namespace,
    $bucket_name,
    $fileName, /* can be stream or filePath or string */
    $filePath, /* optional */
    [], /* optional */
    [
        UploadManagerConstants::PART_SIZE_IN_BYTES => 5*1024
    ]
));

// Example for uploading with string
$content = str_repeat("1234567890", 10000000);
$contentName = "ENTER_YOUR_OBJECT_NAME_HERE";

echo "prepare for uploading string to object storage".PHP_EOL;
$uploadPromiseForString = $upload_manager->upload(UploadManagerRequest::createUploadManagerRequest(
    $namespace,
    $bucket_name,
    $contentName, /* can be stream or filePath or string */
    $content, /* optional */
    [], /* optional */
    [
        UploadManagerConstants::PART_SIZE_IN_BYTES => 5*1024
    ]
));

// Example for uploading with stream
$stream = fopen("https://www.google.com", 'r');
$streamName = "ENTER_YOUR_OBJECT_NAME_HERE";

echo "prepare for uploading stream to object storage".PHP_EOL;
$uploadPromiseForStream = $upload_manager->upload(UploadManagerRequest::createUploadManagerRequest(
    $namespace,
    $bucket_name,
    $streamName, /* can be stream or filePath or string */
    $stream, /* optional */
    [], /* optional */
    [
        UploadManagerConstants::PART_SIZE_IN_BYTES => 5*1024
    ]
));

// Example for catching exception and resume
try {
    echo "wait for the promise to finish".PHP_EOL;
    $uploadRespone = $uploadPromiseForFile->wait();
    echo "Upload File Success, response:".PHP_EOL;
    var_dump($uploadRespone);
} catch (MultipartUploadException $e) {
    $resumeInfo = $e->getMultipartResumeInfo();
    echo "Multipart Upload Failed, error detail: ".$e.PHP_EOL;
}
if (isset($resumeInfo)) {
    // ignoring error handling here
    echo "Retry upload using resumeUploadFile function".PHP_EOL;
    echo $resumeInfo;
    $resume_promise = $upload_manager->resumeUploadFromResumeInfo($resumeInfo);
    $resumeResponse = $resume_promise->wait();
    echo "Resume UploadSuccess, response:".PHP_EOL;
    var_dump($resumeResponse);
}

// wait for the other uploads to complete, ignoring errors\
$stringResponse = $uploadPromiseForString->wait();
echo "Upload Success, response: ".PHP_EOL;
var_dump($stringResponse);

$streamResponse = $uploadPromiseForStream->wait();
echo "Upload Success, response: ".PHP_EOL;
var_dump($streamResponse);

echo "Upload Manage Example End".PHP_EOL;
