<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use GuzzleHttp\Promise\Promise;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use GuzzleHttp\Promise\PromisorInterface;

abstract class AbstractUploader implements PromisorInterface
{
    protected $client;

    protected $uploadManagerRequest;

    protected $promise;

    public function __construct(ObjectStorageAsyncClient $client, UploadManagerRequest &$uploadManagerRequest)
    {
        $this->client = $client;
        $this->uploadManagerRequest = $uploadManagerRequest;
    }

    public function promise()
    {
        if ($this->promise) {
            return $this->promise;
        }
        return $this->promise = $this->prepareUpload();
    }

    protected function initUploadRequest()
    {
        return array_merge($this->uploadManagerRequest->getExtras(), [
            'namespaceName' => $this->uploadManagerRequest->getNamespace(),
            'bucketName' => $this->uploadManagerRequest->getBucketName(),
            'objectName' => $this->uploadManagerRequest->getObjectName(),
        ]);
    }

    abstract protected function prepareUpload();
}
