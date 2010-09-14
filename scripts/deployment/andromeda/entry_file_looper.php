<?php
ini_set("memory_limit","256M");

$file_name = @$argv[1];
if(!$file_name || !file_exists($file_name))
	die('wrong file name'.PHP_EOL);

$f = fopen($file_name, 'r');

$line_count = 0;
$last_line_sent = 0;
while($line = fgets($f))
{
	$line_count++;
	$line = rtrim($line, PHP_EOL);
	if(!$line) 
	{
		echo 'skipped line '.$line_count.' line empty'.PHP_EOL;
		continue;
	}

	$entry_list[] = $line;
	if(count($entry_list) == 200)
	{
		echo 'sending 200 entries from line '.$last_line_sent.' to '.$line_count.PHP_EOL;
		$error = null;
		$output = array();
		$strEntryList = implode(',', $entry_list);
		$command = 'php entryMigrationFromFile.php "'.$strEntryList.'"';
		exec($command, $output, $error);
		$entry_list = array();
		$last_line_sent = $line_count;
	}
}

