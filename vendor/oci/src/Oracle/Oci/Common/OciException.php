<?php

namespace Oracle\Oci\Common;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class OciException extends RuntimeException
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class OciBadResponseException extends OciException
{
    private $response;

    protected $statusCode;
    protected $errorCode;
    protected $message;
    protected $opcRequestId;
    protected $targetService;

    protected $operationName;
    protected $timestamp;
    protected $requestEndpoint;
    protected $clientVersion;

    protected $operationReferenceLink;
    protected $errorTroubleshootingLink;

    public function __construct(ResponseInterface &$response)
    {
        $this->response = $response;
        $this->statusCode = $response->getStatusCode();
        $bodyContents = $response->getBody()->getContents();
        if ($bodyContents != null && strlen($bodyContents) > 0) {
            $json = json_decode($response->getBody());
            $this->errorCode = $json->code;
            $this->message = $json->message;
        } else {
            $this->errorCode = null;
            $this->message = "The service returned HTTP status code {$this->statusCode}.";
        }
        $this->opcRequestId = $response->getHeader('opc-request-id')[0];

        parent::__construct($this->message, $this->statusCode);

        # TODO
        $targetService = "";
        $operationName = "";
        $timestamp = "";
        $requestEndpoint = "";
        $clientVersion = "";
        $operationReferenceLink = "";
        $errorTroubleshootingLink = "";
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getOpcRequestId()
    {
        return $this->opcRequestId;
    }

    public function __toString()
    {
        $service = $this->targetService != null ? $this->targetService . " Service" : "Service";
        return "Error returned by {$service}. Http Status Code: '{$this->statusCode}'. Error Code: '{$this->errorCode}'. Message: '{$this->message}'. Opc request id: '{$this->opcRequestId}'.";
    }
}
