<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;
use Oracle\Oci\Common\OciItemIterator;
use Oracle\Oci\Common\OciResponse;
use Oracle\Oci\Common\OciResponseIterator;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\Tests\Common\RequestStoringMiddleware;
use PHPUnit\Framework\TestCase;

class IteratorTest extends TestCase
{
    /**
     * @var ObjectStorageClient
     */
    protected $client;

    /**
     * @before
     */
    public function before()
    {
        \Oracle\Oci\Common\Logging\Logger::setGlobalLogAdapter(new \Oracle\Oci\Common\Logging\NoOpLogAdapter());
        // \Oracle\Oci\Common\Logging\Logger::setGlobalLogAdapter(new \Oracle\Oci\Common\Logging\EchoLogAdapter(LOG_INFO, [
        //     "Oracle\\Oci\\Common\\OciItemIterator" => LOG_DEBUG,
        //     "Oracle\\Oci\\Common\\OciResponseIterator" => LOG_DEBUG
        // ]));
        $auth_provider = new ConfigFileAuthProvider();
        $this->client = new ObjectStorageClient(
            $auth_provider
        );
    }

    public static function setMockResponse($client, $responses)
    {
        if (!is_array($responses)) {
            $responses = [$responses];
        }
        $mock = new MockHandler($responses);
        $client->setHandler($mock);
    }

    public static function addRequestStoringMiddleware($client)
    {
        $middleware = new RequestStoringMiddleware();
        $client->getStack()->after('signingMiddleware', $middleware->storeRequests(), 'RequestStoringMiddleware');
        return $middleware;
    }

    public function testResponseIterator_SinglePage()
    {
        $page1Body = json_encode([
            ['name' => 'bucket1'],
            ['name' => 'bucket2']
        ]);
        $responses = [
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body)
        ];
        self::setMockResponse($this->client, $responses);
        $requestStoringMiddleware = self::addRequestStoringMiddleware($this->client);

        $originalParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartment123'
        ];
        $it = new OciResponseIterator($this->client, 'listBuckets', $originalParams);
        foreach ($it as $k => $v) {
            $this->assertEquals(0, $k);
            $this->assertEquals(self::buildOciResponse($responses[0]), $v);
        }

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[0]), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertFalse($it->valid());
        $this->assertEquals(1, $it->key());

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[0]), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertFalse($it->valid());
        $this->assertEquals(1, $it->key());

        // Not necessary, foreach automatically rewinds: $it->rewind();
        foreach ($it as $k => $v) {
            $this->assertEquals(0, $k);
            $this->assertEquals(self::buildOciResponse($responses[0]), $v);
        }
        $this->assertFalse($it->valid());
        $this->assertEquals(1, $it->key());

        $requests = $requestStoringMiddleware->getRequests();

        $this->assertEquals(4, count($requests));
        foreach ($requests as $r) {
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());
        }
    }

    public function testResponseIterator_MultiplePages()
    {
        $pageBodies = [
            json_encode([
                ['name' => 'bucket1'],
                ['name' => 'bucket2']
            ]),
            json_encode([
                ['name' => 'bucket3'],
                ['name' => 'bucket4']
            ]),
            json_encode([
                ['name' => 'bucket5'],
                ['name' => 'bucket6']
            ])
        ];
        $responses = [
            // // first pass through
            new Response(200, ['opc-next-page' => 'page2'], $pageBodies[0]),
            new Response(200, ['opc-next-page' => 'page3'], $pageBodies[1]),
            new Response(200, [], $pageBodies[2]),
            // second pass through
            new Response(200, ['opc-next-page' => 'page2'], $pageBodies[0]),
            new Response(200, ['opc-next-page' => 'page3'], $pageBodies[1]),
            new Response(200, [], $pageBodies[2]),
            // third pass through
            new Response(200, ['opc-next-page' => 'page2'], $pageBodies[0]),
            new Response(200, ['opc-next-page' => 'page3'], $pageBodies[1]),
            new Response(200, [], $pageBodies[2]),
            // 4th pass through
            new Response(200, ['opc-next-page' => 'page2'], $pageBodies[0]),
            new Response(200, ['opc-next-page' => 'page3'], $pageBodies[1]),
            new Response(200, [], $pageBodies[2]),
            // 5th (partial) pass
            new Response(200, ['opc-next-page' => 'page2'], $pageBodies[0]),
            new Response(200, ['opc-next-page' => 'page3'], $pageBodies[1]),
            // 6th (partial) pass
            new Response(200, ['opc-next-page' => 'page2'], $pageBodies[0]),
        ];
        self::setMockResponse($this->client, $responses);
        $requestStoringMiddleware = self::addRequestStoringMiddleware($this->client);

        $it = new OciResponseIterator($this->client, 'listBuckets', [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartment123'
        ]);

        $responseIndex = 0;

        $index = 0;
        foreach ($it as $k => $v) {
            $this->assertEquals($index, $k);
            $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $v);
            ++$index;
        }
        $this->assertEquals(3, $it->key());

        $it->rewind();

        // second pass through
        $this->assertEquals(0, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $it->next();
        $this->assertEquals(1, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $it->next();
        $this->assertEquals(2, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $it->next();
        $this->assertFalse($it->valid());
        $this->assertEquals(3, $it->key());

        $it->rewind();

        // third pass through
        $this->assertEquals(0, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $it->next();
        $this->assertEquals(1, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $it->next();
        $this->assertEquals(2, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $it->next();
        $this->assertFalse($it->valid());
        $this->assertEquals(3, $it->key());

        // // Not necessary, foreach automatically rewinds: $it->rewind();
        
        // 4th pass through
        $index = 0;
        foreach ($it as $k => $v) {
            $this->assertEquals($index, $k);
            $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $v);
            ++$index;
        }
        $this->assertFalse($it->valid());
        $this->assertEquals(3, $it->key());

        $it->rewind();
        
        // 5th (partial) pass
        $this->assertEquals(0, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $it->next();
        $this->assertEquals(1, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());


        $it->rewind();
        
        // 6th (partial) pass
        $this->assertEquals(0, $it->key());
        $this->assertEquals(self::buildOciResponse($responses[$responseIndex++]), $it->current());
        $this->assertTrue($it->valid());

        $requests = $requestStoringMiddleware->getRequests();

        $this->assertEquals(15, count($requests));
        $requestIndex = 0;
        for ($pass = 0; $pass<4; ++$pass) {
            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page2", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page3", $r->getUri()->getQuery());
        }
        // 5th (partial) pass
        $r = $requests[$requestIndex++];
        $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
        $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());

        $r = $requests[$requestIndex++];
        $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
        $this->assertEquals("compartmentId=compartment123&page=page2", $r->getUri()->getQuery());

        // 6th (partial) pass
        $r = $requests[$requestIndex++];
        $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
        $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());
    }

    private static function buildOciResponse($guzzleResponse)
    {
        return new OciResponse(
            $guzzleResponse->getStatusCode(),
            $guzzleResponse->getHeaders(),
            null,
            json_decode($guzzleResponse->getBody())
        );
    }

    // item iterators

    public function testItemIterator_SinglePage_Empty()
    {
        $page1Body = json_encode([]);
        $responses = [
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body)
        ];
        self::setMockResponse($this->client, $responses);
        $requestStoringMiddleware = self::addRequestStoringMiddleware($this->client);

        $originalParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartment123'
        ];
        $it = new OciItemIterator($this->client, 'listBuckets', $originalParams);
        foreach ($it as $k => $v) {
            $this->fail("There are no items in this list.");
        }

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());
        
        $it->next();
        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());
        
        $it->next();
        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());

        // Not necessary, foreach automatically rewinds: $it->rewind();
        foreach ($it as $k => $v) {
            $this->fail("There are no items in this list.");
        }
        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());

        $requests = $requestStoringMiddleware->getRequests();

        $this->assertEquals(4, count($requests));
        foreach ($requests as $r) {
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());
        }
    }

    public function testItemIterator_SinglePage_SingleItem()
    {
        $item1 = ['name' => 'bucket1'];
        $page1Body = json_encode([
            $item1
        ]);
        $responses = [
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body)
        ];
        self::setMockResponse($this->client, $responses);
        $requestStoringMiddleware = self::addRequestStoringMiddleware($this->client);

        $originalParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartment123'
        ];
        $it = new OciItemIterator($this->client, 'listBuckets', $originalParams);
        foreach ($it as $k => $v) {
            $this->assertEquals(0, $k);
            $this->assertEquals(json_decode(json_encode($item1)), $v);
        }

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($item1)), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertEquals(1, $it->key());
        $this->assertFalse($it->valid());

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($item1)), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertEquals(1, $it->key());
        $this->assertFalse($it->valid());

        // Not necessary, foreach automatically rewinds: $it->rewind();
        foreach ($it as $k => $v) {
            $this->assertEquals(0, $k);
            $this->assertEquals(json_decode(json_encode($item1)), $v);
        }
        $this->assertEquals(1, $it->key());
        $this->assertFalse($it->valid());

        $requests = $requestStoringMiddleware->getRequests();

        $this->assertEquals(4, count($requests));
        foreach ($requests as $r) {
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());
        }
    }

    public function testItemIterator_SinglePage_MultipleItems()
    {
        $page1Items = [
            ['name' => 'bucket1'],
            ['name' => 'bucket2'],
            ['name' => 'bucket3'],
        ];
        $page1Body = json_encode($page1Items);
        $responses = [
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, [], $page1Body)
        ];
        self::setMockResponse($this->client, $responses);
        $requestStoringMiddleware = self::addRequestStoringMiddleware($this->client);

        $originalParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartment123'
        ];
        $it = new OciItemIterator($this->client, 'listBuckets', $originalParams);
        $itemIndex = 0;
        foreach ($it as $k => $v) {
            $this->assertEquals($itemIndex, $k);
            $this->assertEquals(json_decode(json_encode($page1Items[$itemIndex])), $v);
            ++$itemIndex;
        }

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[0])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(1, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[1])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(2, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[2])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertEquals(3, $it->key());
        $this->assertFalse($it->valid());

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[0])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(1, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[1])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(2, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[2])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertEquals(3, $it->key());
        $this->assertFalse($it->valid());

        // Not necessary, foreach automatically rewinds: $it->rewind();
        $itemIndex = 0;
        foreach ($it as $k => $v) {
            $this->assertEquals($itemIndex, $k);
            $this->assertEquals(json_decode(json_encode($page1Items[$itemIndex])), $v);
            ++$itemIndex;
        }
        $this->assertEquals(3, $it->key());
        $this->assertFalse($it->valid());

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[0])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(1, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[1])), $it->current());
        $this->assertTrue($it->valid());

        $it->rewind(); // partial pass

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($page1Items[0])), $it->current());
        $this->assertTrue($it->valid());

        $requests = $requestStoringMiddleware->getRequests();

        $this->assertEquals(6, count($requests));
        foreach ($requests as $r) {
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());
        }
    }

    public function testItemIterator_MultiplePages_Empty()
    {
        $page1Body = json_encode([]);
        $responses = [
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page1Body),
            new Response(200, [], $page1Body),
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page1Body),
            new Response(200, [], $page1Body)
        ];
        self::setMockResponse($this->client, $responses);
        $requestStoringMiddleware = self::addRequestStoringMiddleware($this->client);

        $originalParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartment123'
        ];
        $it = new OciItemIterator($this->client, 'listBuckets', $originalParams);
        foreach ($it as $k => $v) {
            $this->fail("There are no items in this list.");
        }

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());
        
        $it->next();
        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());
        
        $it->next();
        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());

        // Not necessary, foreach automatically rewinds: $it->rewind();
        foreach ($it as $k => $v) {
            $this->fail("There are no items in this list.");
        }
        $this->assertEquals(0, $it->key());
        $this->assertFalse($it->valid());

        $requests = $requestStoringMiddleware->getRequests();

        $this->assertEquals(12, count($requests));
        $requestIndex = 0;
        for ($pass = 0; $pass<4; ++$pass) {
            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page2", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page3", $r->getUri()->getQuery());
        }
    }

    public function testItemIterator_MultiplePages_MultipleItems()
    {
        $item1 = ['name' => 'bucket1'];
        $item2 = ['name' => 'bucket2'];
        $item3 = ['name' => 'bucket3'];
        $expectedItems = [$item1, $item2, $item3];

        $page1Items = [];
        $page2Items = [];
        $page3Items = [$item1, $item2];
        $page4Items = [];
        $page5Items = [];
        $page6Items = [$item3];
        $page7Items = [];

        $page1Body = json_encode($page1Items);
        $page2Body = json_encode($page2Items);
        $page3Body = json_encode($page3Items);
        $page4Body = json_encode($page4Items);
        $page5Body = json_encode($page5Items);
        $page6Body = json_encode($page6Items);
        $page7Body = json_encode($page7Items);

        $responses = [
            // first pass
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page2Body),
            new Response(200, ['opc-next-page' => 'page4'], $page3Body),
            new Response(200, ['opc-next-page' => 'page5'], $page4Body),
            new Response(200, ['opc-next-page' => 'page6'], $page5Body),
            new Response(200, ['opc-next-page' => 'page7'], $page6Body),
            new Response(200, ['other' => 'something'], $page7Body),
            // second pass
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page2Body),
            new Response(200, ['opc-next-page' => 'page4'], $page3Body),
            new Response(200, ['opc-next-page' => 'page5'], $page4Body),
            new Response(200, ['opc-next-page' => 'page6'], $page5Body),
            new Response(200, ['opc-next-page' => 'page7'], $page6Body),
            new Response(200, ['other' => 'something'], $page7Body),
            // third
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page2Body),
            new Response(200, ['opc-next-page' => 'page4'], $page3Body),
            new Response(200, ['opc-next-page' => 'page5'], $page4Body),
            new Response(200, ['opc-next-page' => 'page6'], $page5Body),
            new Response(200, ['opc-next-page' => 'page7'], $page6Body),
            new Response(200, ['other' => 'something'], $page7Body),
            // fourth
            new Response(200, ['opc-next-page' => 'page2'], $page1Body),
            new Response(200, ['opc-next-page' => 'page3'], $page2Body),
            new Response(200, ['opc-next-page' => 'page4'], $page3Body),
            new Response(200, ['opc-next-page' => 'page5'], $page4Body),
            new Response(200, ['opc-next-page' => 'page6'], $page5Body),
            new Response(200, ['opc-next-page' => 'page7'], $page6Body),
            new Response(200, ['other' => 'something'], $page7Body),
        ];
        self::setMockResponse($this->client, $responses);
        $requestStoringMiddleware = self::addRequestStoringMiddleware($this->client);

        $originalParams = [
            'namespaceName' => 'namespace123',
            'compartmentId' => 'compartment123'
        ];
        $it = new OciItemIterator($this->client, 'listBuckets', $originalParams);
        $itemIndex = 0;
        foreach ($it as $k => $v) {
            $this->assertEquals($itemIndex, $k);
            $this->assertEquals(json_decode(json_encode($expectedItems[$itemIndex])), $v);
            ++$itemIndex;
        }

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($expectedItems[0])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(1, $it->key());
        $this->assertEquals(json_decode(json_encode($expectedItems[1])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(2, $it->key());
        $this->assertEquals(json_decode(json_encode($expectedItems[2])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertEquals(3, $it->key());
        $this->assertFalse($it->valid());

        $it->rewind();

        $this->assertEquals(0, $it->key());
        $this->assertEquals(json_decode(json_encode($expectedItems[0])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(1, $it->key());
        $this->assertEquals(json_decode(json_encode($expectedItems[1])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();

        $this->assertEquals(2, $it->key());
        $this->assertEquals(json_decode(json_encode($expectedItems[2])), $it->current());
        $this->assertTrue($it->valid());
        
        $it->next();
        $this->assertEquals(3, $it->key());
        $this->assertFalse($it->valid());

        // Not necessary, foreach automatically rewinds: $it->rewind();
        $itemIndex = 0;
        foreach ($it as $k => $v) {
            $this->assertEquals($itemIndex, $k);
            $this->assertEquals(json_decode(json_encode($expectedItems[$itemIndex])), $v);
            ++$itemIndex;
        }
        $this->assertEquals(3, $it->key());
        $this->assertFalse($it->valid());

        $requests = $requestStoringMiddleware->getRequests();

        $this->assertEquals(7 * 4, count($requests));
        $requestIndex = 0;
        for ($pass = 0; $pass<4; ++$pass) {
            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page2", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page3", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page4", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page5", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page6", $r->getUri()->getQuery());

            $r = $requests[$requestIndex++];
            $this->assertEquals("/n/namespace123/b/", $r->getUri()->getPath());
            $this->assertEquals("compartmentId=compartment123&page=page7", $r->getUri()->getQuery());
        }
    }
}
