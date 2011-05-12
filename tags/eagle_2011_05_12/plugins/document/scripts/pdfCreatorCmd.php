<?php
if ($argc < 4) {
	echo 'wrong usage of this script. usage: ' . $_SERVER ['SCRIPT_NAME'] . ' {inputFile} {outFile} [--readOnly]' . PHP_EOL;
	die ();
}
if (end ( $argv ) == '--readonly') {
	$readonly = true;
	$inputFile = $argv [$argc - 3];
	$outputFile = $argv [$argc - 2];
	$commandArgc = $argc - 4;
} else {
	$readonly = false;
	$inputFile = $argv [$argc - 2];
	$outputFile = $argv [$argc - 1];
	$commandArgc = $argc - 3;
}
$command = "";
for($i = 1; $i <= $commandArgc; $i ++) {
	$command .= $argv [$i].' ';
}

$command .= ' /NoStart ';
$inputFileExtension = pathinfo ( $inputFile, PATHINFO_EXTENSION );
$inputFileExtension = strtolower ( $inputFileExtension );

if (($readonly) && ($inputFileExtension == 'pdf')) {
	$command .= '/IF"' . $inputFile . '" /OF"' . $outputFile . '.pdf"';
} else {
	$command .= '/PF"' . $inputFile . '"';
}

echo "\ncommand: $command";
exec ( $command ); //TODO - return a value. and exit with this value
