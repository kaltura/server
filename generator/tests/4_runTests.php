<?php

require_once(__DIR__ . '/utils.php');

function runJavaTests($clientRoot)
{
	global $config;

	$jdkPath = $config['java']['jdk_path'];
	$externalJars = listDir("{$clientRoot}/lib");

	if (!is_dir("{$clientRoot}/bin"))
		mkdir("{$clientRoot}/bin");

	chdir("{$clientRoot}/bin");

	// compile the client library
	executeCommand("{$jdkPath}javac.exe", "-d . -sourcepath ../src -cp ".implode(';', addPrefix($externalJars, '../lib/'))." ../src/com/kaltura/client/test/KalturaTestSuite.java");

	// pack the client library
	executeCommand("{$jdkPath}jar.exe", "cvf kalturaClient.jar .");

	// run the tests
	copy("{$clientRoot}/src/DemoImage.jpg", "{$clientRoot}/bin/DemoImage.jpg");
	copy("{$clientRoot}/src/DemoVideo.flv", "{$clientRoot}/bin/DemoVideo.flv");
	
	$log4jConfig = fixSlashes("{$clientRoot}/src/log4j/log4j.properties");
	if ($log4jConfig[1] == ':')
		$log4jConfig = substr($log4jConfig, 2);		
	$log4jParam = "-Dlog4j.configuration=file://{$log4jConfig}";
	
	$jarList = "bin/kalturaClient.jar;".implode(';', addPrefix($externalJars, 'lib/'));
	
	chdir($clientRoot);
	executeCommand("{$jdkPath}java.exe", "-cp {$jarList} {$log4jParam} org.junit.runner.JUnitCore com.kaltura.client.test.KalturaTestSuite");
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
	$exeFile = fixSlashes("{$clientRoot}/KalturaClientTester/bin/Debug/KalturaClientTester.exe");
	if (file_exists($exeFile))
		unlink($exeFile);
	
	// compile
	executeCommandFrom($clientRoot, $config['csharp']['devenv_bin'], "/build Debug KalturaClient.sln");
	
	// wait for compilation to end
	$startTime = microtime(true);
	while (microtime(true) - $startTime < 30)
	{
		if (file_exists($exeFile))
			break;
		sleep(1);
	}
	
	if (!file_exists($exeFile))
	{
		echo "Error: failed to compile {$exeFile}\n";
		return;
	}
	
	// run the tests
	executeCommandFrom("{$clientRoot}/KalturaClientTester", $exeFile);
}

if ($argc < 2)
	die("Usage:\n\tphp " . basename(__file__) . " <root dir>\n");
	
$rootDir = fixSlashes($argv[1]);

$config = parse_ini_file(dirname(__file__) . '/config.ini', true);

// C#
echo "C#\n==================\n";
runCSharpTests("{$rootDir}/csharp");

// Java
echo "Java\n==================\n";
runJavaTests("{$rootDir}/java");

// Php5
echo "Php5\n==================\n";
executeCommandFrom("{$rootDir}/php5/TestCode", $config['php']['php_bin'], 'TestMain.php');

// Php5Zend
echo "Php5Zend\n==================\n";
executeCommandFrom("{$rootDir}/php5Zend/tests", $config['php']['php_bin'], 'run.php');

// Php53
echo "Php5.3\n==================\n";
executeCommandFrom("{$rootDir}/php53/tests", $config['php']['php_bin'], 'run.php');

// Python
echo "Python\n==================\n";
executeCommandFrom("{$rootDir}/python", $config['python']['python_bin'], 'setup.py install');
executeCommandFrom("{$rootDir}/python/KalturaClient/tests", $config['python']['python_bin'], '-m unittest discover');

// Ruby
echo "Ruby\n==================\n";
executeCommandFrom("{$rootDir}/ruby", null, 'echo y | ' . $config['ruby']['rake_bin'] . ' test');

// Flex3.5 (test compilation only)
echo "Flex3.5\n==================\n";
executeCommandFrom("{$rootDir}/flex35", $config['flex35']['mxmlc_bin'], "-sp tests . -- tests/KalturaClientSample.as");

