<?php
namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\Common\OciException;

class MultipartUploadException extends OciException
{
    protected $multipartResumeInfo;
    protected $failureExceptions = [];

    public function __construct($uploadId, UploadManagerRequest &$uploadManagerRequest, $partsToCommit, $partsToRetry)
    {
        foreach ($partsToRetry as $key => $part) {
            array_push($this->failureExceptions, $part['exception']);
            unset($partsToRetry[$key]['exception']);
        }
        
        $this->multipartResumeInfo = new MultipartResumeInfo($uploadId, $uploadManagerRequest, $partsToCommit, $partsToRetry);

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
    protected $uploadManagerRequest;
    protected $partsToCommit;
    protected $partsToRetry;

    public function __construct($uploadId, UploadManagerRequest &$uploadManagerRequest, $partsToCommit, $partsToRetry)
    {
        $this->uploadId = $uploadId;
        $this->uploadManagerRequest = $uploadManagerRequest;
        $this->partsToCommit = $partsToCommit;
        $this->partsToRetry = $partsToRetry;
    }

    public function getUploadId()
    {
        return $this->uploadId;
    }
    
    public function getNamespace()
    {
        return $this->uploadManagerRequest->getNamespace();
    }

    public function getBucketName()
    {
        return $this->uploadManagerRequest->getBucketName();
    }

    public function getObjectName()
    {
        return $this->uploadManagerRequest->getObjectName();
    }

    public function getUploadManagerRequest()
    {
        return $this->uploadManagerRequest;
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
        return $this->uploadManagerRequest->getExtras();
    }

    public function __toString()
    {
        return "Multipart Resume Info. "."UploadId: ".$this->uploadId.
        " Namespace: ".$this->getNamespace()." BucketName: ".$this->getBucketName()." ObjectName: ".$this->getBucketName().
        " PartsToCommit: ".json_encode($this->partsToCommit)." PartsToRetry: ".json_encode($this->partsToRetry).
        " Extras: ".json_encode($this->getExtras());
    }
}
