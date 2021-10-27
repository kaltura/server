<?php

namespace Oracle\Oci\Common;

class OciResponse
{
    /*int*/ protected $statusCode;
    protected $headers;
    protected $body;
    /*mixed*/ protected $json;

    public function __construct(
        /*int*/ 
        $statusCode,
        $headers,
        $body = null,
        /*mixed*/ 
        $json = null
    )
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->json = $json;

        if (AbstractClient::getGlobalLogAdapter()->isLogEnabled(LOG_DEBUG, __CLASS__)) {
            $str = "OciResponse:" . PHP_EOL;
            $str .= "Status code: " . $this->getStatusCode() . PHP_EOL;
            $str .= "Headers    : " . PHP_EOL;
            foreach ($this->getHeaders() as $name => $values) {
                if (is_array($values)) {
                    $str .= $name . ': ' . implode(', ', $values) . PHP_EOL;
                } else {
                    $str .= $name . ': ' . $values . PHP_EOL;
                }
            }
            if ($this->json == null) {
                $str .= "Body       : " . $this->getBody() . PHP_EOL;
            } else {
                $str .= "JSON Body  : " . json_encode($this->getJson(), JSON_PRETTY_PRINT) . PHP_EOL;
            }
            AbstractClient::getGlobalLogAdapter()->log($str, LOG_DEBUG, [], __CLASS__);
        }
    }

    public function getStatusCode() // : int
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getJson()
    {
        return $this->json;
    }

    public function echoResponse()
    {
        echo "Status code: " . $this->getStatusCode() . PHP_EOL;
        echo "Headers    : " . PHP_EOL;
        foreach ($this->getHeaders() as $name => $values) {
            echo $name . ': ' . implode(', ', $values) . "\r\n";
        }
        if ($this->json == null) {
            echo "Body       : " . $this->getBody() . PHP_EOL;
        } else {
            echo "JSON Body  : " . json_encode($this->getJson(), JSON_PRETTY_PRINT) . PHP_EOL;
        }
    }
}
