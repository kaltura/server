<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;

class SinglePartUploader extends AbstractUploader
{
    public function __construct(ObjectStorageAsyncClient $client, UploadManagerRequest &$uploadManagerRequest)
    {
        parent::__construct($client, $uploadManagerRequest);
    }

    protected function prepareUpload()
    {
        return $this->client->putObjectAsync(array_merge([
            'putObjectBody' => $this->uploadManagerRequest->getSource(),
        ], $this->initUploadRequest()));
    }
}
