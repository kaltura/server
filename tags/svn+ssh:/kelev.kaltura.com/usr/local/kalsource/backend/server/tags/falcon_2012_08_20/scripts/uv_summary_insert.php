<?php

function usage()
{
	echo "\nusage: uv_summary_insert [cookie/ip] [date yyyy-mm-dd] [path - may use wildcard]\n";
	die;
}


$uv_type = @$argv[1];

if ($uv_type == "cookie")
	$table_name = "unique_visitors_cookie";
else if ($uv_type == "ip")
	$table_name = "unique_visitors_ip";
else
{
	echo "invalid unique visitors type [$uv_type]- use either cookie or ip\n";
	usage();
}

$date = @$argv[2];
$path = @$argv[3];

if (strlen($date) != 10 || !$path)
{
	usage();
}


$files = glob($path);

echo "delete from $table_name where date='$date';\n";
		
foreach($files as $file)
{
	$data = file_get_contents($file);
	$lines = explode("\n", $data);

	foreach($lines as $line)
	{
		if ($line == "")
			continue;
			
		if ($uv_type == "ip")
		{
			$value = ip2long($line);
			if ($value >= 0x80000000) $value -= 0x100000000;
		}
		else
			$value = $line;
		
		echo "insert into $table_name values('$value','$date');\n";
	}
}

?>
