<?php

function normalizeKey($str)
{
	return preg_replace('/[^a-z0-9_]/', '_', strtolower($str));
}

function loadFile($fileName, $keyCount, $cache)
{
	$handle = fopen($fileName, "r");
	if (!$handle)
	{
		die("failed to load file {$fileName}\n");
	}

	$count = 0;
	for (;;)
	{
		$data = fgetcsv($handle);
		if (!$data)
		{
			break;
		}
		
		if (count($data) < $keyCount + 2 || in_array('-', $data))
		{
			continue;
		}
		
		$key = array_slice($data, 0, $keyCount);
		$key = 'coord_' . normalizeKey(implode('_', $key));
		
		$value = array_slice($data, $keyCount, 2);
		$value = implode('/', $value);

		if (!$cache->set($key, $value))
		{
			die("memcache set failed\n");
		}
		$count++;
	}
	
	fclose($handle);
	
	echo "loaded {$count} values from {$fileName}\n";
}

if ($argc < 3)
{
	die("Usage:\n\t".basename(__file__)." <memcache host> <memcache port>\n");
}

$cache = new Memcache();
if (!$cache->connect($argv[1], $argv[2]))
{
	die("failed to connect to memcache\n");
}

loadFile("countriesCoordinates.txt", 1, $cache);
loadFile("citiesCoordinates.txt", 3, $cache);
echo "done\n";
