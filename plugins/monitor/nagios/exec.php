<?php
define('NAGIOS_CODE_OK', 0);
define('NAGIOS_CODE_WARNING', 1);
define('NAGIOS_CODE_CRITICAL', 2);
define('NAGIOS_CODE_UNKNOWN', 3);


$kalturaRootPath = realpath(__DIR__ . '/../../../');

require_once "$kalturaRootPath/tests/monitoring/KalturaMonitorResult.php";
if($argc == 1)
{
	echo "usage...";
}

$systemConfig = parse_ini_file("$kalturaRootPath/configurations/system.template.ini");

$errorThreshold = null;
$warningThreshold = null;

$options = getopt('e:w:');
if(isset($options['e']))
	$errorThreshold = $options['e'];
if(isset($options['w']))
	$warningThreshold = $options['w'];

$testScriptCmd = implode(' ', array_slice($argv, 1));
$testScriptCmd = substr($testScriptCmd, 2);

$outputLines = null;
$returnedValue = null;
$output = exec($systemConfig['PHP_BIN'] . ' ' . $testScriptCmd, $outputLines, $returnedValue);
if($returnedValue !== 0)
{
	echo $output;
	exit(NAGIOS_CODE_UNKNOWN);
}

$xml = implode("\n", $outputLines);
$monitorResult = KalturaMonitorResult::fromXml($xml);

if($monitorResult->errors)
{
	$strErr = '';
	foreach($monitorResult->errors as $error)
	{
		$strErr .= $error->description;
	}
	echo $strErr;
	exit(NAGIOS_CODE_UNKNOWN);
}

if(isset($errorThreshold) && $monitorResult->value > $errorThreshold)
{
	echo 'ERROR -  monitor value: ' . $monitorResult->value . ' exceeded error value: ' . $errorThreshold;
	exit(NAGIOS_CODE_CRITICAL);
}

if(isset($warningThreshold) && $monitorResult->value > $warningThreshold)
{
	echo 'WARNING -  monitor value: ' . $monitorResult->value . ' exceeded warning value: ' . $warningThreshold;
	exit(NAGIOS_CODE_WARNING);
}

echo 'OK - ' . $monitorResult->description;
exit(NAGIOS_CODE_OK);


