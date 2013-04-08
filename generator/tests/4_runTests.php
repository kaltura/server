<?php

require_once('utils.php');

function runJavaTests($clientRoot)
{
	global $config;

	$jdkPath = $config['java']['jdk_path'];
	$externalJars = listDir("{$clientRoot}/lib");

	if (!is_dir("{$clientRoot}/bin"))
		mkdir("{$clientRoot}/bin");

	chdir("{$clientRoot}/bin");

	// unpack external jars
	foreach ($externalJars as $externalJar)
	{
		executeCommand("\"{$jdkPath}jar.exe\" xf ../lib/{$externalJar}");
	}

	// compile the client library
	executeCommand("\"{$jdkPath}javac.exe\" -d . -sourcepath ../src -cp ".implode(';', addPrefix($externalJars, '../lib/'))." ../src/com/kaltura/client/test/KalturaTestSuite.java");

	// repack
	executeCommand("\"{$jdkPath}jar.exe\" cvf full.jar .");

	// run the tests
	copy("{$clientRoot}/src/DemoImage.jpg", "{$clientRoot}/bin/DemoImage.jpg");
	copy("{$clientRoot}/src/DemoVideo.flv", "{$clientRoot}/bin/DemoVideo.flv");
	
	chdir($clientRoot);
	executeCommand("\"{$jdkPath}java.exe\" -cp bin/full.jar org.junit.runner.JUnitCore com.kaltura.client.test.KalturaTestSuite");
}

function runCSharpTests($clientRoot)
{
	global $config;

	// upgrade the solution to a new version
	$search = array(
		'Microsoft Visual Studio Solution File, Format Version 10.00',
		'# Visual C# Express 2008',
		' ToolsVersion="3.5" ',
		'<TargetFrameworkVersion>v2.0</TargetFrameworkVersion>',
		);

	$replace = array(
		'Microsoft Visual Studio Solution File, Format Version ' . $config['csharp']['solution_format_version'],
		'# ' . $config['csharp']['visual_studio_version'],
		' ToolsVersion="' . $config['csharp']['visual_studio_tools_version'] . '" ',
		'<TargetFrameworkVersion>v' . $config['csharp']['dot_net_framework_version'] . '</TargetFrameworkVersion>',
		);

	replaceInFolder($clientRoot, array('.sln', '.csproj'), null, $search, $replace);

	// clean up
	$exeFile = str_replace('\\', '/', "{$clientRoot}/KalturaClientTester/bin/Debug/KalturaClientTester.exe");
	if (file_exists($exeFile))
		unlink($exeFile);
	
	// compile
	$devenvBinary = $config['csharp']['devenv_bin'];
	executeCommandFrom($clientRoot, "\"{$devenvBinary}\" /build Debug KalturaClient.sln");
	
	// wait for compilation to end
	$startTime = microtime(true);
	while (microtime(true) - $startTime < 30)
	{
		if (file_exists($exeFile))
			break;
		sleep(1);
	}
	
	// run the tests
	executeCommandFrom("{$clientRoot}/KalturaClientTester", $exeFile);
}

$config = parse_ini_file(dirname(__file__) . '/config.ini', true);

// C#
echo "C#\n==================\n";
runCSharpTests(dirname(__file__) . '/csharp');

// Java
echo "Java\n==================\n";
runJavaTests(dirname(__file__) . '/java');

// Php5
echo "Php5\n==================\n";
executeCommandFrom(dirname(__file__) . '/php5/TestCode', $config['php']['php_bin'] . ' TestMain.php');

// Php5Zend
echo "Php5Zend\n==================\n";
executeCommandFrom(dirname(__file__) . '/php5Zend/tests', $config['php']['php_bin'] . ' run.php');

// Php53
echo "Php5.3\n==================\n";
executeCommandFrom(dirname(__file__) . '/php53/tests', $config['php']['php_bin'] . ' run.php');

// Python
echo "Python\n==================\n";
executeCommandFrom(dirname(__file__) . '/python/TestCode', $config['python']['python_bin'] . ' PythonTester.py');

// Ruby
echo "Ruby\n==================\n";
executeCommandFrom(dirname(__file__) . '/ruby', $config['ruby']['rake_bin'] . ' test');
