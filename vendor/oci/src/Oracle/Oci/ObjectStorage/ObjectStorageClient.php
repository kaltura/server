<?php

// Generated using OracleSDKGenerator, API Version: 20160918

namespace Oracle\Oci\ObjectStorage;

use InvalidArgumentException;
use Oracle\Oci\Common\AuthProviderInterface;
use Oracle\Oci\Common\HttpUtils;
use Oracle\Oci\Common\OciResponse;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\Common\AbstractClient;

class ObjectStorageClient extends AbstractClient
{
    /*const*/ protected static $endpointTemplate = "https://objectstorage.{region}.{secondLevelDomain}";

    public function __construct(
        AuthProviderInterface $auth_provider,
        $region=null,
        $endpoint=null
    )
    {
        parent::__construct(
            ObjectStorageClient::$endpointTemplate,
            $auth_provider,
            $region,
            $endpoint
        );
    }


    // Should have waiters.

    // Should have paginators.

    // Operation 'abortMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function abortMultipartUpload($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->abortMultipartUpload_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function abortMultipartUpload_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'cancelWorkRequest':
    // path /workRequests/{workRequestId}
    public function cancelWorkRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->cancelWorkRequest_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function cancelWorkRequest_Helper(
        $workRequestId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'commitMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function commitMultipartUpload($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->commitMultipartUpload_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "commitMultipartUploadDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function commitMultipartUpload_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $commitMultipartUploadDetails,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = json_encode($commitMultipartUploadDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'copyObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/copyObject
    public function copyObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->copyObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "copyObjectDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSourceSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSourceSseCustomerKey"),
            HttpUtils::orNull($params, "opcSourceSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId")
        );
    }

    public function copyObject_Helper(
        $namespaceName,
        $bucketName,
        $copyObjectDetails,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSourceSseCustomerAlgorithm = null,
        $opcSourceSseCustomerKey = null,
        $opcSourceSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSourceSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSourceSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSourceSseCustomerAlgorithm));
        }
        if ($opcSourceSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSourceSseCustomerKey", HttpUtils::attemptEncodeParam($opcSourceSseCustomerKey));
        }
        if ($opcSourceSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSourceSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSourceSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/copyObject";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($copyObjectDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'createBucket':
    // path /n/{namespaceName}/b/
    public function createBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "createBucketDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createBucket_Helper(
        $namespaceName,
        $createBucketDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        $__body = json_encode($createBucketDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'createMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function createMultipartUpload($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createMultipartUpload_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createMultipartUploadDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId")
        );
    }

    public function createMultipartUpload_Helper(
        $namespaceName,
        $bucketName,
        $createMultipartUploadDetails,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createMultipartUploadDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'createPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function createPreauthenticatedRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createPreauthenticatedRequest_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createPreauthenticatedRequestDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createPreauthenticatedRequest_Helper(
        $namespaceName,
        $bucketName,
        $createPreauthenticatedRequestDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createPreauthenticatedRequestDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'createReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function createReplicationPolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createReplicationPolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createReplicationPolicyDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createReplicationPolicy_Helper(
        $namespaceName,
        $bucketName,
        $createReplicationPolicyDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createReplicationPolicyDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'createRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function createRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createRetentionRuleDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $createRetentionRuleDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createRetentionRuleDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'deleteBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function deleteBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteBucket_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'deleteObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function deleteObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "versionId")
        );
    }

    public function deleteObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $ifMatch = null,
        $opcClientRequestId = null,
        $versionId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'deleteObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function deleteObjectLifecyclePolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteObjectLifecyclePolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch")
        );
    }

    public function deleteObjectLifecyclePolicy_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $ifMatch = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'deletePreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function deletePreauthenticatedRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deletePreauthenticatedRequest_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "parId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deletePreauthenticatedRequest_Helper(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/{parId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{parId}', utf8_encode($parId), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'deleteReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function deleteReplicationPolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteReplicationPolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "replicationId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteReplicationPolicy_Helper(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{replicationId}', utf8_encode($replicationId), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'deleteRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function deleteRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{retentionRuleId}', utf8_encode($retentionRuleId), $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function getBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "fields")
        );
    }

    public function getBucket_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $fields = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($fields != null) {
            HttpUtils::encodeArray($__query, "fields", $fields, "csv");
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getNamespace':
    // path /n/
    public function getNamespace($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getNamespace_Helper(
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "compartmentId")
        );
    }

    public function getNamespace_Helper(
        $opcClientRequestId = null,
        $compartmentId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($compartmentId != null) {
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeParam($compartmentId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/";

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getNamespaceMetadata':
    // path /n/{namespaceName}
    public function getNamespaceMetadata($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getNamespaceMetadata_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getNamespaceMetadata_Helper(
        $namespaceName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function getObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "versionId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "range"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "httpResponseContentDisposition"),
            HttpUtils::orNull($params, "httpResponseCacheControl"),
            HttpUtils::orNull($params, "httpResponseContentType"),
            HttpUtils::orNull($params, "httpResponseContentLanguage"),
            HttpUtils::orNull($params, "httpResponseContentEncoding"),
            HttpUtils::orNull($params, "httpResponseExpires")
        );
    }

    public function getObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $versionId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $range = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $httpResponseContentDisposition = null,
        $httpResponseCacheControl = null,
        $httpResponseContentType = null,
        $httpResponseContentLanguage = null,
        $httpResponseContentEncoding = null,
        $httpResponseExpires = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($range != null) {
            HttpUtils::addToArray($__headers, "range", HttpUtils::attemptEncodeParam($range));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }
        if ($httpResponseContentDisposition != null) {
            HttpUtils::addToArray($__query, "httpResponseContentDisposition", HttpUtils::attemptEncodeParam($httpResponseContentDisposition));
        }
        if ($httpResponseCacheControl != null) {
            HttpUtils::addToArray($__query, "httpResponseCacheControl", HttpUtils::attemptEncodeParam($httpResponseCacheControl));
        }
        if ($httpResponseContentType != null) {
            HttpUtils::addToArray($__query, "httpResponseContentType", HttpUtils::attemptEncodeParam($httpResponseContentType));
        }
        if ($httpResponseContentLanguage != null) {
            HttpUtils::addToArray($__query, "httpResponseContentLanguage", HttpUtils::attemptEncodeParam($httpResponseContentLanguage));
        }
        if ($httpResponseContentEncoding != null) {
            HttpUtils::addToArray($__query, "httpResponseContentEncoding", HttpUtils::attemptEncodeParam($httpResponseContentEncoding));
        }
        if ($httpResponseExpires != null) {
            HttpUtils::addToArray($__query, "httpResponseExpires", HttpUtils::attemptEncodeParam($httpResponseExpires));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            $__response->getBody(),
            null
        );
    }

    // Operation 'getObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function getObjectLifecyclePolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getObjectLifecyclePolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getObjectLifecyclePolicy_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function getPreauthenticatedRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getPreauthenticatedRequest_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "parId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getPreauthenticatedRequest_Helper(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/{parId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{parId}', utf8_encode($parId), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function getReplicationPolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getReplicationPolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "replicationId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getReplicationPolicy_Helper(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{replicationId}', utf8_encode($replicationId), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function getRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{retentionRuleId}', utf8_encode($retentionRuleId), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'getWorkRequest':
    // path /workRequests/{workRequestId}
    public function getWorkRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getWorkRequest_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getWorkRequest_Helper(
        $workRequestId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'headBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function headBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->headBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function headBucket_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->head(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'headObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function headObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->headObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "versionId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256")
        );
    }

    public function headObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $versionId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__response = $this->client->head(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listBuckets':
    // path /n/{namespaceName}/b/
    public function listBuckets($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listBuckets_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "compartmentId", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "fields"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listBuckets_Helper(
        $namespaceName,
        $compartmentId,
        $limit = null,
        $page = null,
        $fields = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($compartmentId != null) {
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeParam($compartmentId));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($fields != null) {
            HttpUtils::encodeArray($__query, "fields", $fields, "csv");
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listMultipartUploadParts':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function listMultipartUploadParts($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listMultipartUploadParts_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listMultipartUploadParts_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listMultipartUploads':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function listMultipartUploads($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listMultipartUploads_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listMultipartUploads_Helper(
        $namespaceName,
        $bucketName,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listObjectVersions':
    // path /n/{namespaceName}/b/{bucketName}/objectversions
    public function listObjectVersions($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listObjectVersions_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "prefix"),
            HttpUtils::orNull($params, "start"),
            HttpUtils::orNull($params, "end"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "delimiter"),
            HttpUtils::orNull($params, "fields"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "startAfter"),
            HttpUtils::orNull($params, "page")
        );
    }

    public function listObjectVersions_Helper(
        $namespaceName,
        $bucketName,
        $prefix = null,
        $start = null,
        $end = null,
        $limit = null,
        $delimiter = null,
        $fields = null,
        $opcClientRequestId = null,
        $startAfter = null,
        $page = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($prefix != null) {
            HttpUtils::addToArray($__query, "prefix", HttpUtils::attemptEncodeParam($prefix));
        }
        if ($start != null) {
            HttpUtils::addToArray($__query, "start", HttpUtils::attemptEncodeParam($start));
        }
        if ($end != null) {
            HttpUtils::addToArray($__query, "end", HttpUtils::attemptEncodeParam($end));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($delimiter != null) {
            HttpUtils::addToArray($__query, "delimiter", HttpUtils::attemptEncodeParam($delimiter));
        }
        if ($fields != null) {
            HttpUtils::addToArray($__query, "fields", HttpUtils::attemptEncodeParam($fields));
        }
        if ($startAfter != null) {
            HttpUtils::addToArray($__query, "startAfter", HttpUtils::attemptEncodeParam($startAfter));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/objectversions";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listObjects':
    // path /n/{namespaceName}/b/{bucketName}/o
    public function listObjects($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listObjects_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "prefix"),
            HttpUtils::orNull($params, "start"),
            HttpUtils::orNull($params, "end"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "delimiter"),
            HttpUtils::orNull($params, "fields"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "startAfter")
        );
    }

    public function listObjects_Helper(
        $namespaceName,
        $bucketName,
        $prefix = null,
        $start = null,
        $end = null,
        $limit = null,
        $delimiter = null,
        $fields = null,
        $opcClientRequestId = null,
        $startAfter = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($prefix != null) {
            HttpUtils::addToArray($__query, "prefix", HttpUtils::attemptEncodeParam($prefix));
        }
        if ($start != null) {
            HttpUtils::addToArray($__query, "start", HttpUtils::attemptEncodeParam($start));
        }
        if ($end != null) {
            HttpUtils::addToArray($__query, "end", HttpUtils::attemptEncodeParam($end));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($delimiter != null) {
            HttpUtils::addToArray($__query, "delimiter", HttpUtils::attemptEncodeParam($delimiter));
        }
        if ($fields != null) {
            HttpUtils::addToArray($__query, "fields", HttpUtils::attemptEncodeParam($fields));
        }
        if ($startAfter != null) {
            HttpUtils::addToArray($__query, "startAfter", HttpUtils::attemptEncodeParam($startAfter));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listPreauthenticatedRequests':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function listPreauthenticatedRequests($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listPreauthenticatedRequests_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectNamePrefix"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listPreauthenticatedRequests_Helper(
        $namespaceName,
        $bucketName,
        $objectNamePrefix = null,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($objectNamePrefix != null) {
            HttpUtils::addToArray($__query, "objectNamePrefix", HttpUtils::attemptEncodeParam($objectNamePrefix));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listReplicationPolicies':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function listReplicationPolicies($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listReplicationPolicies_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listReplicationPolicies_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listReplicationSources':
    // path /n/{namespaceName}/b/{bucketName}/replicationSources
    public function listReplicationSources($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listReplicationSources_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listReplicationSources_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationSources";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listRetentionRules':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function listRetentionRules($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listRetentionRules_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "page")
        );
    }

    public function listRetentionRules_Helper(
        $namespaceName,
        $bucketName,
        $page = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listWorkRequestErrors':
    // path /workRequests/{workRequestId}/errors
    public function listWorkRequestErrors($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequestErrors_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listWorkRequestErrors_Helper(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}/errors";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listWorkRequestLogs':
    // path /workRequests/{workRequestId}/logs
    public function listWorkRequestLogs($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequestLogs_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listWorkRequestLogs_Helper(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}/logs";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'listWorkRequests':
    // path /workRequests
    public function listWorkRequests($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequests_Helper(
            HttpUtils::orNull($params, "compartmentId", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listWorkRequests_Helper(
        $compartmentId,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($compartmentId != null) {
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeParam($compartmentId));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests";

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'makeBucketWritable':
    // path /n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable
    public function makeBucketWritable($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->makeBucketWritable_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function makeBucketWritable_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'putObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function putObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->putObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "putObjectBody", true),
            HttpUtils::orNull($params, "contentLength"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "expect"),
            HttpUtils::orNull($params, "contentMD5"),
            HttpUtils::orNull($params, "contentType"),
            HttpUtils::orNull($params, "contentLanguage"),
            HttpUtils::orNull($params, "contentEncoding"),
            HttpUtils::orNull($params, "contentDisposition"),
            HttpUtils::orNull($params, "cacheControl"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId"),
            HttpUtils::orNull($params, "storageTier"),
            HttpUtils::orNull($params, "opcMeta")
        );
    }

    public function putObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $putObjectBody,
        $contentLength = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $expect = null,
        $contentMD5 = null,
        $contentType = null,
        $contentLanguage = null,
        $contentEncoding = null,
        $contentDisposition = null,
        $cacheControl = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null,
        $storageTier = null,
        $opcMeta = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($expect != null) {
            HttpUtils::addToArray($__headers, "expect", HttpUtils::attemptEncodeParam($expect));
        }
        if ($contentLength != null) {
            HttpUtils::addToArray($__headers, "contentLength", HttpUtils::attemptEncodeParam($contentLength));
        }
        if ($contentMD5 != null) {
            HttpUtils::addToArray($__headers, "contentMD5", HttpUtils::attemptEncodeParam($contentMD5));
        }
        if ($contentType != null) {
            HttpUtils::addToArray($__headers, "contentType", HttpUtils::attemptEncodeParam($contentType));
        }
        if ($contentLanguage != null) {
            HttpUtils::addToArray($__headers, "contentLanguage", HttpUtils::attemptEncodeParam($contentLanguage));
        }
        if ($contentEncoding != null) {
            HttpUtils::addToArray($__headers, "contentEncoding", HttpUtils::attemptEncodeParam($contentEncoding));
        }
        if ($contentDisposition != null) {
            HttpUtils::addToArray($__headers, "contentDisposition", HttpUtils::attemptEncodeParam($contentDisposition));
        }
        if ($cacheControl != null) {
            HttpUtils::addToArray($__headers, "cacheControl", HttpUtils::attemptEncodeParam($cacheControl));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }
        if ($storageTier != null) {
            HttpUtils::addToArray($__headers, "storageTier", HttpUtils::attemptEncodeParam($storageTier));
        }
        if ($opcMeta != null) {
            HttpUtils::encodeMap($__headers, "opcMeta", "", $opcMeta);
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = $putObjectBody;

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'putObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function putObjectLifecyclePolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->putObjectLifecyclePolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "putObjectLifecyclePolicyDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch")
        );
    }

    public function putObjectLifecyclePolicy_Helper(
        $namespaceName,
        $bucketName,
        $putObjectLifecyclePolicyDetails,
        $opcClientRequestId = null,
        $ifMatch = null,
        $ifNoneMatch = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($putObjectLifecyclePolicyDetails);

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'reencryptBucket':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt
    public function reencryptBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->reencryptBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function reencryptBucket_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/reencrypt";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'reencryptObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}
    public function reencryptObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->reencryptObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "reencryptObjectDetails", true),
            HttpUtils::orNull($params, "versionId"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function reencryptObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $reencryptObjectDetails,
        $versionId = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = json_encode($reencryptObjectDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'renameObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/renameObject
    public function renameObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->renameObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "renameObjectDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function renameObject_Helper(
        $namespaceName,
        $bucketName,
        $renameObjectDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/renameObject";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($renameObjectDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'restoreObjects':
    // path /n/{namespaceName}/b/{bucketName}/actions/restoreObjects
    public function restoreObjects($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->restoreObjects_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "restoreObjectsDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function restoreObjects_Helper(
        $namespaceName,
        $bucketName,
        $restoreObjectsDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/restoreObjects";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($restoreObjectsDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'updateBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function updateBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "updateBucketDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateBucket_Helper(
        $namespaceName,
        $bucketName,
        $updateBucketDetails,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($updateBucketDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'updateNamespaceMetadata':
    // path /n/{namespaceName}
    public function updateNamespaceMetadata($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateNamespaceMetadata_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "updateNamespaceMetadataDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateNamespaceMetadata_Helper(
        $namespaceName,
        $updateNamespaceMetadataDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        $__body = json_encode($updateNamespaceMetadataDetails);

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'updateObjectStorageTier':
    // path /n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier
    public function updateObjectStorageTier($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateObjectStorageTier_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "updateObjectStorageTierDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateObjectStorageTier_Helper(
        $namespaceName,
        $bucketName,
        $updateObjectStorageTierDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($updateObjectStorageTierDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'updateRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function updateRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "updateRetentionRuleDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $updateRetentionRuleDetails,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{retentionRuleId}', utf8_encode($retentionRuleId), $__path);

        $__body = json_encode($updateRetentionRuleDetails);

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }

    // Operation 'uploadPart':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function uploadPart($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->uploadPart_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "uploadPartNum", true),
            HttpUtils::orNull($params, "uploadPartBody", true),
            HttpUtils::orNull($params, "contentLength"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "expect"),
            HttpUtils::orNull($params, "contentMD5"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId")
        );
    }

    public function uploadPart_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $uploadPartNum,
        $uploadPartBody,
        $contentLength = null,
        $opcClientRequestId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $expect = null,
        $contentMD5 = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($expect != null) {
            HttpUtils::addToArray($__headers, "expect", HttpUtils::attemptEncodeParam($expect));
        }
        if ($contentLength != null) {
            HttpUtils::addToArray($__headers, "contentLength", HttpUtils::attemptEncodeParam($contentLength));
        }
        if ($contentMD5 != null) {
            HttpUtils::addToArray($__headers, "contentMD5", HttpUtils::attemptEncodeParam($contentMD5));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }
        if ($uploadPartNum != null) {
            HttpUtils::addToArray($__query, "uploadPartNum", HttpUtils::attemptEncodeParam($uploadPartNum));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = $uploadPartBody;

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    }
}
