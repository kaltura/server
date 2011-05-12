<?php
$filename = 'C:\var\log\batch\dwh.log';
$sleep = 1;
$run = true;

$server = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
if (!$server)
{
  echo "$errstr ($errno)\n";
  exit -1;
}

$clients = array();
while($run)
{
	$client = @stream_socket_accept($server, $sleep);
	if(!$client)
		continue;
		
	$clients[] = $client;
//	echo "Client [" . (count($clients) - 1) . "] connected\n";
	
	clearstatcache(false, $filename);
	$pointer = filesize($filename);
	while(count($clients))
	{
//		sleep($sleep);
		$client = @stream_socket_accept($server, $sleep);
		if($client)
		{
//			echo "Client [" . (count($clients) - 1) . "] connected\n";
			$clients[] = $client;
		}
			
		clearstatcache(false, $filename);
		$nextPointer = filesize($filename);
		if($nextPointer == $pointer)
			continue;
			
		$lines = file_get_contents($filename, false, null, $pointer);
		$lines = explode("\n", $lines);
		foreach($lines as $line)
		{
			if(!strlen($line))
				continue;
				
//			echo "Write bytes [" . strlen($line) . "] to [" . count($clients) . "]\n";
			foreach($clients as $index => $client)
			{
				$written = @fwrite($client, $line . chr(0));
				var_dump($written);
				if(!$written)
				{
//					echo "Client [$index] disconnected\n";
					unset($clients[$index]);
				}
			}
		}
			
		$pointer = $nextPointer;
	}
}