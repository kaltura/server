<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use GuzzleHttp\Promise;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use UploadManagerConstants;

abstract class AbstractMultipartUploader extends AbstractUploader
{
    protected $config;

    protected $uploadId;

    protected $partsToCommit = [];

    protected $partsToRetry = [];

    public function __construct(ObjectStorageAsyncClient $client, $namespace, $bucketName, $objectName, $source, $extras, array $config = [])
    {
        parent::__construct($client, $namespace, $bucketName, $objectName, $source, $extras);
        $this->config = $config;
    }

    public function promise()
    {
        if ($this->promise) {
            return $this->promise;
        }

        return $this->promise = Promise\Each::ofLimit(
            $this->prepareUpload(),
            $this->config[UploadManagerConstants::ALLOW_PARALLEL_UPLOADS] ?
                $this->config[UploadManagerConstants::CONCURRENCY] : 1
        )->then(
            function () {
                if (count($this->partsToRetry) > 0) {
                    throw new MultipartUploadException(
                        $this->uploadId,
                        $this->namespace,
                        $this->bucketName,
                        $this->objectName,
                        $this->source,
                        $this->partsToCommit,
                        $this->partsToRetry,
                        $this->extras
                    );
                }
                return $this->partsToCommit;
            }
        );
    }
}
