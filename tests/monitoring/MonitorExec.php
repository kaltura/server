<?php
require_once realpath(__DIR__ ) . '/XmlHelper.php';
if($argc == 1) {
	echo "usage...";
}

$options = getopt("e::w::");

$errorVal = $options["e"];
$warningVal = $options["w"];

$testScript = $argv[1];
$testScriptCmd = implode(" ", array_slice($argv, 1));
$testScriptCmd = substr($testScriptCmd,2);


$resStr = exec(@PHP_BIN@ . ' ' . @APP_DIR@ . '/testing/monitoring/' . $testScriptCmd);
$res = XmlHelper::fromXmlResult($resStr);

if ($res->errors) {
	$strErr = '';
	foreach ($res->errors as $error) {
		$strErr .= $error->description;
	}
	echo $strErr;
	exit (3);
} if (isset($errorVal) && $res->value > $errorVal) {
	echo 'ERROR -  monitor value: ' . $res->value  . ' exceeded error value: ' . $errorVal;
	exit (2);
} else if (isset($warningVal) && $res->value > $warningVal) {
	echo 'WARNING -  monitor value: ' . $res->value  . ' exceeded warning value: ' . $warningVal;
	exit (1);
} 
echo 'OK - ' . $res->description;
exit (0);


