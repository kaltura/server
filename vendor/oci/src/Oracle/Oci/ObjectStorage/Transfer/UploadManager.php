<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use Oracle\Oci\Common\OciException;
use RuntimeException;
use UploadManagerConstants;

class UploadManager
{
    protected $client;
    protected $config;

    public function __construct(ObjectStorageAsyncClient $objectStorageClient, array $config = UploadManagerConstants::DEFAULT_CONFIG)
    {
        $this->client = $objectStorageClient;
        $this->config = $config + UploadManagerConstants::DEFAULT_CONFIG;
    }

    // Upload the provided file to object storage
    // This function will automatically determine the way to upload to object storage based on the upload manager configuration
    // Default will split the file to 5MB per part and allow concurrency
    // Returns a guzzle promise
    public function uploadFile($namespace, $bucketName, $objectName, $path, $extras = [])
    {
        $scheme = SELF::determineScheme($path);
        if ($scheme !== 'file') {
            throw new InvalidArgumentException("Not supported scheme for uploadFile: $scheme");
        }
        $path = realpath($path);
        if (!file_exists($path)) {
            throw new InvalidArgumentException("File does not exist: $path");
        }

        $fileSize = filesize($path);
        if ($this->config[UploadManagerConstants::ALLOW_MULTIPART_UPLOADS] && $fileSize > $this->config[UploadManagerConstants::PART_SIZE_IN_BYTES]) {
            $uploadId = $this->createMultipartUpload($namespace, $bucketName, $objectName, $extras);
            return $this->multipartUpload($uploadId, $namespace, $bucketName, $objectName, $path, $extras);
        } else {
            return $this->fileUpload($namespace, $bucketName, $objectName, $path, $extras);
        }
    }

    // Resume upload for failed parts to object storage
    // This function will require the uploadId returned from createMultipartUpload request
    // partsToCommit: A list of parts to be commited after the upload, should contains partNum and etag
    // partsToRetry: A list of parts to be uploaded, should contains partNum, length and position
    // These data can be retrieved from the MultipartUploadException thrown during multipart upload
    // Returns a guzzle promise
    public function resumeUploadFile($uploadId, $namespace, $bucketName, $objectName, $path, $partsToCommit, $partsToRetry, $extras = [])
    {
        $extras = array_merge($extras, ['partsToCommit'=>$partsToCommit, 'partsToRetry'=>$partsToRetry]);
        return $this->multipartUpload($uploadId, $namespace, $bucketName, $objectName, $path, $extras);
    }

    // Resume upload for failed parts to object storage using the info from MultipartResumeInfo
    // MultipartResumeInfo can be retrieved from the MultipartUploadException thrown during mutipart upload
    // Returns a guzzle promise
    public function resumeUploadFileFromResumeInfo(MultipartResumeInfo $resumeInfo)
    {
        return $this->resumeUploadFile(
            $resumeInfo->getUploadId(),
            $resumeInfo->getNamespace(),
            $resumeInfo->getBucketName(),
            $resumeInfo->getObjectName(),
            $resumeInfo->getPath(),
            $resumeInfo->getPartsToCommit(),
            $resumeInfo->getPartsToRetry(),
            $resumeInfo->getExtras()
        );
    }

    private static function determineScheme($path)
    {
        if (is_string($path)) {
            return !strpos($path, '://') ? 'file' : explode('://', $path)[0];
        }
        throw new InvalidArgumentException("Please provide file path to upload the file");
    }

    // Abort upload
    // This function require the uploadId returned from createMultipartUpload request
    // Returns a guzzle promise
    public function abortUpload($uploadId, $namespace, $bucketName, $objectName, $extras = [])
    {
        $params = array_merge([
            'namespaceName'=>$namespace,
            'bucketName'=>$bucketName,
            'uploadId'=>$uploadId,
            'objectName'=>$objectName,
        ], $extras);
        return $this->client->abortMultipartUploadAsync($params);
    }

    private function fileUpload($namespace, $bucketName, $objectName, $filePath, $extras)
    {
        $fileContent = file_get_contents($filePath);
        if (!$fileContent) {
            throw new OciException("Unable to read file content");
        }
        $response = new SinglePartUploader($this->client, $namespace, $bucketName, $objectName, $fileContent, $extras);
        return $response->promise();
    }

    private function multipartUpload($uploadId, $namespace, $bucketName, $objectName, $filePath, $extras)
    {
        return (new MultipartFileUploader($this->client, $namespace, $bucketName, $objectName, $uploadId, $filePath, $extras, $this->config))->promise()->then(
            function ($partsToCommit) use ($namespace, $bucketName, $objectName, $uploadId, $extras) {
                return $this->commitMultipartUpload($namespace, $bucketName, $objectName, $uploadId, $partsToCommit, $extras);
            },
            function ($e) {
                // we'll not perform retry directly, instead throw the exception and users can decide what to do next
                throw $e;
            }
        );
    }

    private function createMultipartUpload($namespace, $bucketName, $objectName, $extras)
    {
        $params = [
            'namespaceName'=>$namespace,
            'bucketName'=>$bucketName,
            'createMultipartUploadDetails'=>[
                'object'=> $objectName
            ]
        ];
        $response = $this->client->createMultipartUploadAsync(array_merge($params, $extras))->wait();
        return $response->getJson()->uploadId;
    }

    private function commitMultipartUpload($namespace, $bucketName, $objectName, $uploadId, $partsToCommit, $extras = [])
    {
        $params = array_merge([
            'namespaceName'=>$namespace,
            'bucketName'=>$bucketName,
            'uploadId'=>$uploadId,
            'objectName'=>$objectName,
            'commitMultipartUploadDetails'=>[
                'partsToCommit'=>$partsToCommit
            ],
        ], $extras);

        return $this->client->commitMultipartUploadAsync($params);
    }
}
