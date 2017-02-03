<?php

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

class ExclusiveScriptExecutor
{
    private $ttl;
    private $pathToScript;
    private $cacheHandler;
    private $cacheName=kCacheManager::CACHE_TYPE_LOCK_KEYS;

    public function __construct($pathToScript,$ttl=3600)
    {
        $this->pathToScript=$pathToScript;
        $this->ttl=$ttl;
        $this->cacheHandler=$this->initCache();
    }

    public function execute()
    {
        if ($this->tryLock($this->pathToScript,$this->ttl))
        {
            print("\nExclusively running script {".$this->pathToScript."} with TTL {".$this->ttl."} \n");
            $output = shell_exec($this->pathToScript);
            print ("\n".$output );
        }
        else
        {
            print("\nCommand ".$this->pathToScript." is locked for execution.\n");
        }
    }

    private function tryLock($key,$ttl)
    {
        return $this->cacheHandler->add($key,$key." is locked for ".$ttl,$ttl);
    }

    private function initCache()
    {
        $cache = kCacheManager::getSingleLayerCache($this->cacheName);
        if(!$cache)
            throw new Exception ("Could not allocate cache named - ".$this->cacheName);

        return $cache;
    }
}


if (in_array($argv[1],array('usage','?','help','-h')) || $argc!=3)
{
    $strMsg = "This script is used for single execution of another script , even if it is called from several process or devices at the same time.\n".
              "The execution will be locked using memcache.\n".
              "Usage -  $argv[0] <script to run> <ttl>\n".
              "Example: $argv[0] 'ls -ltr' 100\n".
              " The command 'ls -ltr' will be executed once , then it could be executed until 100 seconds will pass.\n";
    print ($strMsg);
    exit(0);
}

$pathToScript = $argv[1];
$ttl          = $argv[2];
$executor = new ExclusiveScriptExecutor($pathToScript,$ttl);
$executor->execute();