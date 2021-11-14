<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;
use Oracle\Oci\Common\Auth\RefreshableOnNotAuthenticatedInterface;
use Oracle\Oci\Common\OciBadResponseException;
use PHPUnit\Framework\TestCase;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;

class AuthRefreshTest extends TestCase
{
    public function before()
    {
        \Oracle\Oci\Common\Logging\Logger::setGlobalLogAdapter(new \Oracle\Oci\Common\Logging\NoOpLogAdapter());
    }

    public static function setMockResponse($client, $responses)
    {
        if (!is_array($responses)) {
            $responses = [$responses];
        }
        $mock = new MockHandler($responses);
        $client->setHandler($mock);
    }

    public function testRefresh_refreshableAuth()
    {
        $body = json_encode([]);
        $responses = [
            new Response(401, [], $body),
            new Response(200, [], $body)
        ];
        $auth_provider = new DummyRefreshableAuthProvider();
        $client = new ObjectStorageClient($auth_provider);
        self::setMockResponse($client, $responses);

        $requestParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartmentId123'
        ];
        $this->assertEquals(false, $auth_provider->refreshResult);
        $response = $client->listBuckets($requestParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(true, $auth_provider->refreshResult);
    }

    public function testRefresh_refreshableAuth_nonRefreshableCode()
    {
        $body = json_encode([
            'code'=>'NonRefreshableCode',
            'message'=>'NonRefreshable Auth'
        ]);
        $responses = [
            new Response(401, ['opc-request-id'=>'123'], $body),
            new Response(200, [], $body)
        ];
        $auth_provider = new DummyRefreshableAuthProvider();
        $client = new ObjectStorageClient($auth_provider);
        self::setMockResponse($client, $responses);

        $requestParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartmentId123'
        ];
        $this->assertEquals(false, $auth_provider->refreshResult);
        try {
            $response = $client->listBuckets($requestParams);
        } catch (OciBadResponseException $e) {
            $this->assertEquals(404, $e->getStatusCode());
            $this->assertEquals('NonRefreshableCode', $e->getErrorCode());
            $this->assertEquals(false, $auth_provider->refreshResult);
        }
    }

    public function testRefresh_nonRefreshableAuth()
    {
        $body = json_encode([
            'code'=>'NonRefreshable',
            'message'=>'NonRefreshable Auth'
        ]);
        $responses = [
            new Response(401, ['opc-request-id'=>'123'], $body),
            new Response(200, [], $body)
        ];
        $auth_provider = new ConfigFileAuthProvider();
        $client = new ObjectStorageClient($auth_provider);
        self::setMockResponse($client, $responses);

        $requestParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartmentId123'
        ];

        try {
            $response = $client->listBuckets($requestParams);
        } catch (OciBadResponseException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('NonRefreshable', $e->getErrorCode());
        }
    }

    public function testRefresh_nonRefreshableAuth_nonRefreshableCode()
    {
        $body = json_encode([
            'code'=>'NonRefreshableCode',
            'message'=>'NonRefreshable Auth'
        ]);
        $responses = [
            new Response(404, ['opc-request-id'=>'123'], $body),
            new Response(200, [], $body)
        ];
        $auth_provider = new ConfigFileAuthProvider();
        $client = new ObjectStorageClient($auth_provider);
        self::setMockResponse($client, $responses);

        $requestParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartmentId123'
        ];

        try {
            $response = $client->listBuckets($requestParams);
        } catch (OciBadResponseException $e) {
            $this->assertEquals(404, $e->getStatusCode());
            $this->assertEquals('NonRefreshableCode', $e->getErrorCode());
        }
    }
}

class DummyRefreshableAuthProvider extends ConfigFileAuthProvider implements RefreshableOnNotAuthenticatedInterface
{
    public $refreshResult = false;

    public function isRefreshableOnNotAuthenticated()
    {
        return true;
    }

    public function refresh()
    {
        $this->refreshResult = true;
    }
}
