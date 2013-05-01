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

$systemConfig = parse_ini_file("$kalturaRootPath/configurations/system.ini");

$errorThresholdMax = null;
$errorThresholdMin = null;
$warningThresholdMax = null;
$warningThresholdMin = null;

$options = getopt('', array(
	'error-threshold:',
	'warning-threshold:',
));
$matches = null;
if(isset($options['error-threshold']))
{
	if(preg_match('/^([\d]*)-([\d]*)$/', trim($options['error-threshold']), $matches))
	{
		if(is_numeric($matches[1]))
			$errorThresholdMin = $matches[1];
		if(is_numeric($matches[2]))
			$errorThresholdMax = $matches[2];
	}
	elseif(is_numeric($options['error-threshold']))
	{
		$errorThresholdMax = intval($options['error-threshold']);
	}
}
if(isset($options['warning-threshold']))
{
	if(preg_match('/^([\d]*)-([\d]*)$/', trim($options['warning-threshold']), $matches))
	{
		if(is_numeric($matches[1]))
			$warningThresholdMin = $matches[1];
		if(is_numeric($matches[2]))
			$warningThresholdMax = $matches[2];
	}
	elseif(is_numeric($options['warning-threshold']))
	{
		$warningThresholdMax = intval($options['warning-threshold']);
	}
}

$testScriptCmd = implode(' ', array_slice($argv, 1));

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

if(isset($errorThresholdMax) && $monitorResult->value > $errorThresholdMax)
{
	echo 'ERROR -  monitor value: ' . $monitorResult->value . ' exceeded error value: ' . $errorThresholdMax;
	exit(NAGIOS_CODE_CRITICAL);
}

if(isset($warningThresholdMax) && $monitorResult->value > $warningThresholdMax)
{
	echo 'WARNING -  monitor value: ' . $monitorResult->value . ' exceeded warning value: ' . $warningThresholdMax;
	exit(NAGIOS_CODE_WARNING);
}

if(isset($errorThresholdMin) && $monitorResult->value > $errorThresholdMin)
{
	echo 'ERROR -  monitor value: ' . $monitorResult->value . ' exceeded error value: ' . $errorThresholdMin;
	exit(NAGIOS_CODE_CRITICAL);
}

if(isset($warningThresholdMin) && $monitorResult->value > $warningThresholdMin)
{
	echo 'WARNING -  monitor value: ' . $monitorResult->value . ' exceeded warning value: ' . $warningThresholdMin;
	exit(NAGIOS_CODE_WARNING);
}

echo 'OK - ' . $monitorResult->description;
exit(NAGIOS_CODE_OK);


