<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use Exception;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use Oracle\Oci\Common\OciException;
use phpDocumentor\Reflection\Types\Resource_;
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

    // Upload the provided stream to object storage
    // This function will automatically determine the way to upload to object storage based on the upload manager configuration
    // Default will split the contnet to 5MB per part and allow concurrency
    // Returns a guzzle promise
    public function upload(UploadManagerRequest $uploadManagerRequest)
    {
        $uploadManagerRequest->updateUploadConfig($this->config);
        $size = $uploadManagerRequest->getSize();
        if ($uploadManagerRequest->getUploadConfig()[UploadManagerConstants::ALLOW_MULTIPART_UPLOADS]) {
            if ($size == -1 || $size > $uploadManagerRequest->getUploadConfig()[UploadManagerConstants::PART_SIZE_IN_BYTES]) {
                return $this->multipartUpload($uploadManagerRequest);
            }
        }
        return $this->singlePartUpload($uploadManagerRequest);
    }

    // Resume upload for failed parts to object storage
    // This function will require the uploadId returned from createMultipartUpload request
    // partsToCommit: A list of parts to be commited after the upload, should contains partNum and etag
    // partsToRetry: A list of parts to be uploaded, should contains partNum, length and position
    // These data can be retrieved from the MultipartUploadException thrown during multipart upload
    // Returns a guzzle promise
    public function resumeUpload($uploadId, UploadManagerRequest $uploadManagerRequest, $partsToCommit, $partsToRetry)
    {
        if (empty($partsToRetry)) {
            throw new InvalidArgumentException("Resume Upload require partsToRetry");
        }
        return $this->multipartResumeUpload($uploadId, $uploadManagerRequest, $partsToCommit, $partsToRetry);
    }

    // Resume upload for failed parts to object storage using the info from MultipartResumeInfo
    // MultipartResumeInfo can be retrieved from the MultipartUploadException thrown during mutipart upload
    // Returns a guzzle promise
    public function resumeUploadFromResumeInfo(MultipartResumeInfo $resumeInfo)
    {
        $rewindResult = rewind($resumeInfo->getUploadManagerRequest()->getSource());
        if (!$rewindResult) {
            throw new OciException("Unable to rewind the source, resume from resumeInfo not supported");
        }
        return $this->resumeUpload(
            $resumeInfo->getUploadId(),
            $resumeInfo->getUploadManagerRequest(),
            $resumeInfo->getPartsToCommit(),
            $resumeInfo->getPartsToRetry()
        );
    }

    // Abort upload
    // This function require the uploadId returned from createMultipartUpload request
    // Returns a guzzle promise
    public function abortUpload($uploadId, UploadManagerRequest $uploadManagerRequest)
    {
        $params = array_merge([
            'namespaceName'=>$uploadManagerRequest->getNamespace(),
            'bucketName'=>$uploadManagerRequest->getBucketName(),
            'uploadId'=>$uploadId,
            'objectName'=>$uploadManagerRequest->getObjectName(),
        ], $uploadManagerRequest->getExtras());
        return $this->client->abortMultipartUploadAsync($params);
    }

    private function singlePartUpload(UploadManagerRequest &$uploadManagerRequest)
    {
        $response = new SinglePartUploader($this->client, $uploadManagerRequest);
        return $response->promise();
    }

    private function multipartUpload(UploadManagerRequest &$uploadManagerRequest)
    {
        $uploadId = $this->createMultipartUpload($uploadManagerRequest);
        return (new MultipartStreamUploader($this->client, $uploadId, $uploadManagerRequest))->promise()->then(
            function ($partsToCommit) use ($uploadId, $uploadManagerRequest) {
                return $this->commitMultipartUpload($uploadId, $uploadManagerRequest, $partsToCommit);
            },
            function ($e) {
                // we'll not perform retry directly, instead throw the exception and users can decide what to do next
                throw $e;
            }
        );
    }

    private function multipartResumeUpload($uploadId, UploadManagerRequest &$uploadManagerRequest, $partsToCommit, $partsToRetry)
    {
        return (new MultipartStreamResumeUploader($this->client, $uploadId, $uploadManagerRequest, $partsToCommit, $partsToRetry))->promise()->then(
            function ($partsToCommit) use ($uploadId, $uploadManagerRequest) {
                return $this->commitMultipartUpload($uploadId, $uploadManagerRequest, $partsToCommit);
            },
            function ($e) {
                // we'll not perform retry directly, instead throw the exception and users can decide what to do next
                throw $e;
            }
        );
    }

    private function createMultipartUpload(UploadManagerRequest &$uploadManagerRequest)
    {
        $params = array_merge([
            'namespaceName'=>$uploadManagerRequest->getNamespace(),
            'bucketName'=>$uploadManagerRequest->getBucketName(),
            'createMultipartUploadDetails'=>[
                'object'=> $uploadManagerRequest->getObjectName()
            ]
        ], $uploadManagerRequest->getExtras());
        $response = $this->client->createMultipartUploadAsync($params)->wait();
        return $response->getJson()->uploadId;
    }

    private function commitMultipartUpload($uploadId, UploadManagerRequest &$uploadManagerRequest, $partsToCommit)
    {
        $params = array_merge([
            'namespaceName'=>$uploadManagerRequest->getNamespace(),
            'bucketName'=>$uploadManagerRequest->getBucketName(),
            'uploadId'=>$uploadId,
            'objectName'=>$uploadManagerRequest->getObjectName(),
            'commitMultipartUploadDetails'=>[
                'partsToCommit'=>$partsToCommit
            ],
        ], $uploadManagerRequest->getExtras());

        return $this->client->commitMultipartUploadAsync($params);
    }
}
