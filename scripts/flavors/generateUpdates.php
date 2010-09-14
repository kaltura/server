<?php
$csv = fopen('flavors.csv', 'r');
$sql = fopen('updates.sql', 'w');
$headers = fgetcsv($csv);

$values = fgetcsv($csv);
while($values)
{
	$valuesUpdates = array();
	$thisRecord = array();
	foreach($values as $index => $value)
	{
		$field = $headers[$index];
		$thisRecord[$field] = $value;
		
		if($value == '\N')
			$valuesUpdates[] = "$field = NULL";
		else
			$valuesUpdates[] = "$field = '$value'";
	} 
	$name = $thisRecord['name'];
	$description = $thisRecord['description'];

	$update = "/* $description */\n";
	$update .= "UPDATE 	flavor_params\nSET		";
	$update .= join(",\n		", $valuesUpdates);
	$update .= "\nWHERE	name = '$name'";
	$update .= "\nAND		partner_id = 0";
	$update .= "\nAND		deleted_at is null;\n\n";
			
	fwrite($sql, $update);
	$values = fgetcsv($csv);
}

fclose($sql);
fclose($csv);