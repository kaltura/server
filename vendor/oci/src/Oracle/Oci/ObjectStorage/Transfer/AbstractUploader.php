<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use GuzzleHttp\Promise\Promise;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use GuzzleHttp\Promise\PromisorInterface;

abstract class AbstractUploader implements PromisorInterface
{
    protected $client;

    protected $namespace;

    protected $bucketName;

    protected $objectName;

    protected $source;

    protected $promise;

    protected $extras;

    public function __construct(ObjectStorageAsyncClient $client, $namespace, $bucketName, $objectName, $source, $extras)
    {
        $this->client = $client;
        $this->namespace = $namespace;
        $this->bucketName = $bucketName;
        $this->objectName = $objectName;
        $this->source = $source;
        $this->extras = $extras;
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
        return array_merge($this->extras, [
            'namespaceName' => $this->namespace,
            'bucketName' => $this->bucketName,
            'objectName' => $this->objectName,
        ]);
    }

    abstract protected function prepareUpload();
}
