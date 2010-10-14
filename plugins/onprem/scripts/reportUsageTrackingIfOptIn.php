<?php

define('QUERIES_FILE', realpath(dirname(__FILE__) . '/queries.ini'));
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/alpha/config/kConf.php');

if (!kConf::get('usage_tracking_optin')) {
	echo "Usage tracking is not enabled".PHP_EOL;
	die(0);
}
		
$post_parameters = queryUsageReport(QUERIES_FILE);
$post_parameters['installa_id'] = kConf::get('installation_id');
$post_parameters['report_admin_email'] = kConf::get('report_admin_email');
$post_parameters['package_version'] = kConf::get('kaltura_version');
foreach (array_keys($post_parameters) as $key) {
	echo "$key = $post_parameters[$key]".PHP_EOL;
}
sendReport(kConf::get('usage_tracking_url'), $post_parameters);
die(0);

// runs all the queries in the $queries_file and return an array with all the post parameters
function queryUsageReport($queries_file) {
	$post_parameters = array();

	try {
		$dbs = kConf::getDB();
		$dsn = $dbs['datasources']['dwh']['connection']['dsn'];
		$queries = parse_ini_file($queries_file, true);	
		$dbh = new PDO($dsn);
		
		// perform all the queries
		for ($i = 0; $i < count($queries['query']); $i++) {
			$statement = $dbh->query($queries['query'][$i]);
			if ($statement === false) {
				echo "Failed to execute query: ".$queries['query'][$i].PHP_EOL;
			} else {
				addPostParameters($post_parameters, $statement, $queries['array_name'][$i]);
			}
		}
		$dbh = null;	
	} catch (PDOException $e) {
		echo "Error!: " . $e->getMessage();
	}
	
	return $post_parameters;
}

// add post parameters from a query
function addPostParameters(&$post_parameters, $statement, $array_name) {
	$param_array = array();
	$i = 0;
	
	// traverse all rows
	foreach ($statement as $row) {
		// every column is a parameter, assumed to have only 1 row, otherwise the last rows values will override all others
		if (empty($array_name)) {
			foreach (array_keys($row) as $key) {
				// each column is added twice - on column name and on column number, ignore it by column number
				if (!is_numeric($key)) {
					$post_parameters[$key] = $row[$key];					
				}
			}
		// multirows parameter, will create 2-dimensional array rows x columns (with names)
		} else {		
			$param_array[$i++] = $row;
		}
	}
	
	// set an array parameter with the array name
	if (!empty($array_name)) {
		$post_parameters[$array_name] = $param_array;
	}	
}

// send a usage tracking report with the given $post_parameters
function sendReport($url, $post_parameters) {
	if (extension_loaded("curl")) {		
		// create a new cURL resource
		$ch = curl_init();		
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_parameters);
		
		// grab URL and pass it to the browser
		$result = curl_exec($ch);
		if ($result) {
			echo "Succefully sent usage tracking report".PHP_EOL;
		} else {
			echo "Failed to send usage tracking report".PHP_EOL;		
		}
		
		// close cURL resource, and free up system resources
		curl_close($ch);
	}
}