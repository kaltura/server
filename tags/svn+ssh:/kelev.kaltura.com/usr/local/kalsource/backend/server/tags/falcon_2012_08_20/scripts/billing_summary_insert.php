<?php

function usage()
{
	echo "\nusage: billing_summary_insert [www/ll/l3/ak] [date yyyy-mm-dd] [path - may use wildcard]\n";
	die;
}


$activity = 1;//PartnerActivity::PARTNER_ACTIVITY_TRAFFIC;
$sub_activity_name = @$argv[1];

if ($sub_activity_name == "www")
	$sub_activity = 1;
else if ($sub_activity_name == "ll")
	$sub_activity = 2;
else if ($sub_activity_name == "l3")
	$sub_activity = 3;
else if ($sub_activity_name == "ak")
	$sub_activity = 4;
	
else
{
	echo "invalid sub_activity [$sub_activity]- use either www or ll or l3 or ak\n";
	usage();
}

$date = @$argv[2];
$path = @$argv[3];

if (strlen($date) == 8)
	$date = substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
	
if (strlen($date) != 10 || !$path)
{
	usage();
}


$files = glob($argv[3]);

echo "delete from partner_activity where activity=$activity and sub_activity=$sub_activity and activity_date='$date';\n";
		
foreach($files as $file)
{
	$data = file_get_contents($file);
	$lines = explode("\n", $data);

	foreach($lines as $line)
	{
		$stats = explode(",", $line);
		$partner_id = $stats[0] != "" ? $stats[0] : "null";
		$usage = @$stats[1];

		if ($usage == "")
			continue;
		
		echo "insert into partner_activity (partner_id, activity_date, activity, sub_activity, amount) values($partner_id,'$date',$activity,$sub_activity,$usage);\n";
	}
}

?>
