<?php

namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use UploadManagerConstants;

use function Oracle\Oci\Common\defer;

class MultipartFileUploader extends AbstractMultipartUploader
{
    public function __construct(ObjectStorageAsyncClient $client, $namespace, $bucketName, $objectName, $uploadId, $source, $extras, array $config = [])
    {
        $this->uploadId = $uploadId;
        parent::__construct($client, $namespace, $bucketName, $objectName, $source, $extras, $config);
    }

    protected function prepareUpload()
    {
        foreach ($this->prepareSources() as $source) {
            $params = array_merge($this->initUploadRequest(), [
                'uploadPartNum'=>$source['partNum'],
                'uploadPartBody'=> &$source['content'],
                'contentLength'=> $source['length'],
                'uploadId'=> $this->uploadId,
            ]);
            Logger::logger(static::class)->debug("Preparing for multipart uploading part: ".$params['uploadPartNum']);
            yield $this->client->uploadPartAsync(
                $params
            )->then(function ($response) use ($source) {
                Logger::logger(static::class)->debug("multipart uploading part: ".$source['partNum']." success");
                array_push($this->partsToCommit, [
                        'partNum' => $source['partNum'],
                        'etag' => $response->getHeaders()['etag'][0]
                    ]);
            }, function ($e) use ($source) {
                Logger::logger(static::class)->debug("multipart uploading part: ".$source['partNum']." failed, error details: ".$e);
                array_push($this->partsToRetry, [
                    'partNum' => $source['partNum'],
                    'length' => $source['length'],
                    'position' => $source['position'],
                    'exception' => $e
                ]);
            });
            unset($source);
        }
    }

    private function prepareSources()
    {
        if (isset($this->extras['partsToRetry'])) {
            $this->partsToCommit = $this->extras['partsToCommit'];
            $partsToRetry = $this->extras['partsToRetry'];
            $numberOfPartsToRetry = count($partsToRetry);
    
            for ($i = 0; $i < $numberOfPartsToRetry; $i++) {
                $retryPart = $partsToRetry[$i];
                $content = file_get_contents($this->source, false, null, $retryPart['position'], $retryPart['length']);
                if (!$content) {
                    throw new OciException("Unable to read file content");
                }
                Logger::logger(static::class)->debug("Yielding data for partNum: ".$retryPart['partNum']);
                yield [
                    'partNum' => $retryPart['partNum'],
                    'content' => &$content,
                    'length' => $retryPart['length'],
                    'position' => $retryPart['position']
                ];
            }
        } else {
            $handle = fopen($this->source, 'r');
            if (!$handle) {
                throw new OciException("Unable to open file: $this->source");
            }
            defer($_, function () use ($handle) {
                fclose($handle);
            });
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
}
