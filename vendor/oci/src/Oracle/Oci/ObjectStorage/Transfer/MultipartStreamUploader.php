<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use UploadManagerConstants;

class MultipartStreamUploader extends AbstractMultipartUploader
{
    public function __construct(ObjectStorageAsyncClient $client, $uploadId, UploadManagerRequest &$uploadManagerRequest)
    {
        $this->uploadId = $uploadId;
        parent::__construct($client, $uploadManagerRequest);
    }

    protected function prepareSources()
    {
        $handle = $this->uploadManagerRequest->getSource();
        if (!$handle) {
            throw new OciException("Unable to get the stream");
        }
        $partNum = 1;
        $partSize = $this->config[UploadManagerConstants::PART_SIZE_IN_BYTES];
        while (!feof($handle)) {
            $position = ftell($handle);
            $content = fread($handle, $partSize);
            if (strlen($content) == 0) {
                break;
            }
            Logger::logger(static::class)->debug("Yielding data for partNum: $partNum");
            yield [
                'partNum' => $partNum,
                'content' => &$content,
                'length' => strlen($content),
                'position' => $position,
            ];
            $partNum++;
        }
    }
}
