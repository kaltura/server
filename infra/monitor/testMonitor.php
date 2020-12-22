<?php

class TestIpAddress
{
	private static $cache = null;
	private static $counter = 0;
	private static $sum = array();

	private static function getCache()
	{
		return '192.168.' . rand(1, 254) . '.' . rand(1, 255);
	}
	
	private static function getCount()
	{
		return rand(1, 1000);
	}

	public static function get()
	{
		self::$counter--;
		if(!self::$cache || !self::$counter)
		{
			self::$counter = self::getCount();
			self::$cache = self::getCache();
			if(!isset(self::$sum[self::$cache]))
				self::$sum[self::$cache] = 0;
		}
			
		self::$sum[self::$cache] ++;
		return self::$cache;
	}

	public static function getMax($limit)
	{
		arsort(self::$sum);
		return array_slice(self::$sum, 0, $limit, true);
	}

	public static function getTotal()
	{
		return count(self::$sum);
	}
}

class TestServer
{
	private static $cache = null;
	private static $counter = 0;
	private static $sum = array();

	private static function getCache()
	{
		return 'pa-test' . rand(1, 40);
	}
	
	private static function getCount()
	{
		return rand(1, 1000);
	}
	
	public static function get()
	{
		self::$counter--;
		if(!self::$cache || !self::$counter)
		{
			self::$counter = self::getCount();
			self::$cache = self::getCache();
			if(!isset(self::$sum[self::$cache]))
				self::$sum[self::$cache] = 0;
		}
			
		self::$sum[self::$cache] ++;
		return self::$cache;
	}

	public static function getMax($limit)
	{
		arsort(self::$sum);
		return array_slice(self::$sum, 0, $limit, true);
	}

	public static function getTotal()
	{
		return count(self::$sum);
	}
}

class TestPartner
{
	private static $cache = null;
	private static $counter = 0;
	private static $sum = array();

	private static function getCache()
	{
		return rand(100, 150);
	}
	
	private static function getCount()
	{
		return rand(1, 300);
	}

	public static function get()
	{
		self::$counter--;
		if(!self::$cache || !self::$counter)
		{
			self::$counter = self::getCount();
			self::$cache = self::getCache();
			if(!isset(self::$sum[self::$cache]))
				self::$sum[self::$cache] = 0;
		}
			
		self::$sum[self::$cache] ++;
		return self::$cache;
	}

	public static function getMax($limit)
	{
		arsort(self::$sum);
		return array_slice(self::$sum, 0, $limit, true);
	}

	public static function getTotal()
	{
		return count(self::$sum);
	}
}

class TestAction
{
	private static $cache = null;
	private static $counter = 0;
	private static $sum = array();

	private static $services = array(
		'category',
		'entry',
		'flavorAsset',
		'thumbAsset',
		'captionAsset',
		'attachmentAsset',
		'accessControl',
		'conversionProfile',
	);
	
	private static $actions = array(
		'add',
		'update',
		'list',
		'delete',
		'get',
	);

	private static function getCache()
	{
		return $action = self::$services[rand(1, count(self::$services) - 1)] . '.' . self::$actions[rand(1, count(self::$actions) - 1)];
	}
	
	private static function getCount()
	{
		return rand(1, 1000);
	}
	
	public static function get()
	{
		self::$counter--;
		if(!self::$cache || !self::$counter)
		{
			self::$counter = self::getCount();
			self::$cache = self::getCache();
			if(!isset(self::$sum[self::$cache]))
				self::$sum[self::$cache] = 0;
		}
			
		self::$sum[self::$cache] ++;
		return self::$cache;
	}

	public static function getMax($limit)
	{
		arsort(self::$sum);
		return array_slice(self::$sum, 0, $limit, true);
	}

	public static function getTotal()
	{
		return count(self::$sum);
	}
}

$duration = 60;
if(isset($argv[1]) && is_numeric($argv[1]))
	$duration = $argv[1];
	
$end = time() + $duration;
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$sent = 0;

while(time() < $end)
{
	$data = array(
		'server'		=> TestServer::get(),
		'address'		=> TestIpAddress::get(),
		'partner'		=> TestPartner::get(),
		'action'		=> TestAction::get(),
		'cached'		=> (bool) rand(0, 1),
		'sessionType'	=> rand(-1, 2),
	);

	$msg = json_encode($data);
	socket_sendto($sock, $msg, strlen($msg), 0, '127.0.0.1', 6005);
	usleep(10);
	$sent ++;
}
echo "Sent $sent messages\n";

$total = TestServer::getTotal();
echo "Servers [$total]\n";
$max = TestServer::getMax(6);
foreach($max as $key => $value)
	echo "\t{$key}\t - $value\n";
echo "\n";

$total = TestIpAddress::getTotal();
echo "Addresses [$total]\n";
$max = TestIpAddress::getMax(6);
foreach($max as $key => $value)
	echo "\t{$key}\t - $value\n";
echo "\n";
	
$total = TestPartner::getTotal();
echo "Partners [$total]\n";
$max = TestPartner::getMax(6);
foreach($max as $key => $value)
	echo "\t{$key}\t - $value\n";
echo "\n";
	
$total = TestAction::getTotal();
echo "Actions [$total]\n";
$max = TestAction::getMax(6);
foreach($max as $key => $value)
	echo "\t{$key}\t - $value\n";
echo "\n";
	
socket_close($sock);
