<?php

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

class ExclusiveScriptExecutor
{
    private $ttl;
    private $pathToScript;
    private $cacheHandler;
    private $cacheName=kCacheManager::CACHE_TYPE_LOCK_KEYS;

    public function __construct($lockingTag,$ttl=3600)
    {
        $this->lockingTag=$lockingTag;
        $this->ttl=$ttl;
        $this->cacheHandler=$this->initCache();
    }

    public function execute($commandLine)
    {
        if ($this->tryLock($this->lockingTag,$this->ttl))
        {
            print("\nExclusively running script {".$commandLine."} with TTL {".$this->ttl."} locked with tag {".$this->lockingTag."}\n");
            $output = shell_exec($commandLine);
            print ("\n".$output );
        }
        else
        {
            $value = $this->cacheHandler->get($this->lockingTag);
            print("\nCommand $commandLine is locked for execution. Because tag: $value \n");
        }
    }

    private function tryLock($key,$ttl)
    {
        $hostName=gethostname();
        $prettyTime=$this->getPrettyTime();
        return $this->cacheHandler->add($key,$key." is locked for ".$ttl." seconds by:".$hostName." since:".$prettyTime,$ttl);
    }

    private function getPrettyTime()
    {
        $t = time();
        $prettyTime = date('y-m-d:h-i-s',$t);
        return $prettyTime;
    }

    private function initCache()
    {
        $cache = kCacheManager::getSingleLayerCache($this->cacheName);
        if(!$cache)
            throw new Exception ("\nCould not allocate cache named - ".$this->cacheName."\n");

        return $cache;
    }
}

if ($argc!=4 || in_array($argv[1],array('usage','?','help','-h')))
{
    $strMsg = "\nThis script is used for single execution of a script , even if it is called from several process or devices at the same time.".
              "\nThe execution will be locked using memcache.".
              "\nUsage -  $argv[0] <Locking tag> <script to run> <ttl>".
              "\nExample: $argv[0] LockMe 'ls -ltr' 100\n";
    print ($strMsg);
    exit(0);
}

$lockingTag   = $argv[1];
$commandLine = $argv[2];
$ttl          = $argv[3];
$executor = new ExclusiveScriptExecutor($lockingTag,$ttl);
$executor->execute($commandLine);