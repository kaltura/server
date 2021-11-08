<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;

class SinglePartUploader extends AbstractUploader
{
    public function __construct(ObjectStorageAsyncClient $client, $namespace, $bucketName, $objectName, $source, $extras)
    {
        parent::__construct($client, $namespace, $bucketName, $objectName, $source, $extras);
    }

    protected function prepareUpload()
    {
        return $this->client->putObjectAsync(array_merge([
            'putObjectBody' => $this->source,
        ], $this->initUploadRequest()));
    }
}
