<?php
namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\Common\OciException;

class MultipartUploadException extends OciException
{
    protected $multipartResumeInfo;
    protected $failureExceptions = [];

    public function __construct($uploadId, $namespace, $bucketName, $objectName, $path, $partsToCommit, $partsToRetry, $extras)
    {
        foreach ($partsToRetry as $key => $part) {
            array_push($this->failureExceptions, $part['exception']);
            unset($partsToRetry[$key]['exception']);
        }
        
        $this->multipartResumeInfo = new MultipartResumeInfo($uploadId, $namespace, $bucketName, $objectName, $path, $partsToCommit, $partsToRetry, $extras);

        parent::__construct("Multipart upload failed, recorded the exceptions to failureExceptions property");
    }

    public function __toString()
    {
        return "Multipart upload failed. ".$this->multipartResumeInfo->__toString." failureException: ".json_encode($this->failureExceptions);
    }

    public function getMultipartResumeInfo()
    {
        return $this->multipartResumeInfo;
    }

    public function getFailureExceptions()
    {
        return $this->failureExceptions;
    }
}

class MultipartResumeInfo
{
    protected $uploadId;
    protected $namespace;
    protected $bucketName;
    protected $objectName;
    protected $path;
    protected $partsToCommit;
    protected $partsToRetry;
    protected $extras;

    public function __construct($uploadId, $namespace, $bucketName, $objectName, $path, $partsToCommit, $partsToRetry, $extras=[])
    {
        $this->uploadId = $uploadId;
        $this->namespace = $namespace;
        $this->bucketName = $bucketName;
        $this->objectName = $objectName;
        $this->path = $path;
        $this->partsToCommit = $partsToCommit;
        $this->partsToRetry = $partsToRetry;
        $this->extras = $extras;
    }

    public function getUploadId()
    {
        return $this->uploadId;
    }
    
    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getBucketName()
    {
        return $this->bucketName;
    }

    public function getObjectName()
    {
        return $this->objectName;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPartsToCommit()
    {
        return $this->partsToCommit;
    }

    public function getPartsToRetry()
    {
        return $this->partsToRetry;
    }

    public function getExtras()
    {
        return $this->extras;
    }

    public function __toString()
    {
        return "Multipart Resume Info. "."UploadId: ".$this->uploadId.
        " Namespace: ".$this->namespace." BucketName: ".$this->bucketName." ObjectName: ".$this->bucketName.
        " Path: ".$this->path." PartsToCommit: ".json_encode($this->partsToCommit)." PartsToRetry: ".json_encode($this->partsToRetry).
        " Extras: ".json_encode($this->extras);
    }
}
