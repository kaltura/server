<?php

namespace OracleOci\Auth;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

class CacheTest extends TestCase
{
    const expectedValue = 'value';

    /**
     * @return CacheItemPoolInterface
     */
    public static function getCacheInterface()
    {
        $filesystemAdapter = new Local("/tmp/phpCache");
        $filesystem        = new Filesystem($filesystemAdapter);

        return new FilesystemCachePool($filesystem);
    }

    public function testCachePut()
    {
        $cache = self::getCacheInterface();

        // Get an item (existing or new)
        $item = $cache->getItem('cache_key');

        // Set some values and store
        $item->set(self::expectedValue);
        $item->expiresAfter(10);
        $cache->save($item);

        // Verify existence
        $this->assertTrue($cache->hasItem('cache_key'));
        $this->assertTrue($item->isHit());

        // Get stored values
        $myValue = $item->get();
        $this->assertEquals(self::expectedValue, $myValue);
    }

    public function testCacheGet()
    {
        $cache = self::getCacheInterface();

        // Get an item (existing or new)
        $item = $cache->getItem('cache_key');

        // Verify existence
        $this->assertTrue($cache->hasItem('cache_key'));
        $this->assertTrue($item->isHit());

        // Get stored values
        $myValue = $item->get();
        $this->assertEquals(self::expectedValue, $myValue);
    }

    public function testCacheDelete()
    {
        $cache = self::getCacheInterface();

        // Delete
        $cache->deleteItem('cache_key');
    }
}
