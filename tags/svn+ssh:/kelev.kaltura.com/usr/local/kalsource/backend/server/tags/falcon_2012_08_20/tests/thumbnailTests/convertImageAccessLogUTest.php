<?php
// should run on pa-reports

//require_once 'PHPUnit' . DIRECTORY_SEPARATOR . 'Framework' . DIRECTORY_SEPARATOR . 'TestCase.php';
require_once 'bootstrap.php';
ini_set('memory_limit', '256M');

class convertImageAccessLogUTest //extends PHPUnit_Framework_TestCase
{
	private $localDomain;
	private $remoteDomain;
	private $compareCommand;
	
	public static function doTests()
	{
		$uTest = new convertImageAccessLogUTest();
		
		$data = $uTest->providerTestList();
		$index = 0;
		foreach($data as $url => $testData)
		{
			$index++;
			try
			{
				call_user_func_array(array($uTest, 'testConvertImage'), $testData);
			}
			catch (Exception $e)
			{
				KalturaLog::info("$index [$url]: " . $e->getMessage());
				continue;
			}
			KalturaLog::info("$index [$url]: OK");
		}
	}
	
	public static function fail($message = '')
	{
		throw new Exception($message);
	}
	
	public static function assertGreaterThan($expected, $actual, $message = '')
	{
		if($actual <= $expected)
			self::fail($message);
	}
	
	public static function assertEquals($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = FALSE, $ignoreCase = FALSE)
	{
		if(
			($delta && abs($expected - $actual) > $delta)
			||
			(!$delta && $actual != $expected)
		)
			self::fail($message);
	}
	
	/**
	 * retrieve information from tests file
	 * @return array<array> - each element in the array is equivalent to a single test
	 */
	public function providerTestList()
	{
		$config = parse_ini_file(__FILE__ . '.ini');
		
		$this->remoteDomain = $config['remote_domain'];
		$this->localDomain = $config['local_domain'];
		$this->compareCommand = $config['compare_command'];
		
		$accessLogTemplate = str_split($config['access_log_path']);
		$accessLogCalcTemplate = '';
		foreach($accessLogTemplate as $accessLogTemplateChar)
			$accessLogCalcTemplate .= "\\$accessLogTemplateChar";

		$accessLogCalcTemplate = preg_replace('/\\\\%\\\\(\\w)/', '$1', $accessLogCalcTemplate);
		KalturaLog::debug("Access log template [$accessLogCalcTemplate]");
		$accessLogPath = date($accessLogCalcTemplate);
		KalturaLog::debug("Access log path [$accessLogPath]");
		
		$grep = $config['grep_command'];
		$pattern = $config['grep_pattern'];
		$finalAccessLogPath = __FILE__ . '.access.log';
		$cmd = "$grep $pattern $accessLogPath > $finalAccessLogPath";
		KalturaLog::debug("Executing: $cmd");
		system($cmd);
		
		$lines = file($finalAccessLogPath);
		KalturaLog::debug("Lines [" . count($lines) . "]");
		$maxTests = $config['max_tests'];;
		
		$provedidData = array();
		for($index = 0; $index < count($lines) && $maxTests >= count($provedidData); $index++)
		{
			$line = $lines[$index];
			KalturaLog::debug("Line [$line]");
			$matches = null;
		  	if(!preg_match('/\\/p\\/(\d+)\\/sp\\/\d+\\/thumbnail\\/entry_id\\/(\d_[\d\w]{8})\\/([^\s]+)/', $lines[$index], $matches))
				continue;
				
			//KalturaLog::debug("Matches [" . print_r($matches, true) . "]");
			
			$url = $matches[0];
			$partnerId = $matches[1];
			$entryId = $matches[2];
			$paramsSplit = explode('/', trim($matches[3], '/'));
			$params = array();
			
			while(current($paramsSplit))
			{
				$params[current($paramsSplit)] = next($paramsSplit);
				next($paramsSplit);
			}
			KalturaLog::debug("Params [" . print_r($params, true) . "]");
			
			$data = array($index, $partnerId, $entryId, $params);
			$provedidData[$url] = $data;
		}
			
		return $provedidData;
	}

	private function buildRemoteURL($partnerId, $entryId, array $params)
	{
		$url = "http://{$this->remoteDomain}/p/$partnerId/thumbnail/entry_id/$entryId";
		foreach($params as $key => $value)
			$url .= is_null($value) || !strlen($value) ? '' : "/$key/$value";
		
		return $url;
	}
	
	private function buildLocalURL($entryId, array $params)
	{
		$url = "http://{$this->localDomain}/index.php/extwidget/thumbnail/entry_id/$entryId";
		foreach($params as $key => $value)
			$url .= is_null($value) || !strlen($value) ? '' : "/$key/$value";
		
		return $url;
	}
	
	/**
	 * 
	 * test convertImage functions on both servers
	 * the test is done by executing a number of different tests on two different servers (on identical files)
	 * @dataProvider providerTestList
	 */
	public function testConvertImage($index, $partnerId, $entryId, array $params)
	{
		$downloadedRemote = dirname(__FILE__) . DIRECTORY_SEPARATOR . "remote.$index.jpg";
		$downloadedLocal = dirname(__FILE__) . DIRECTORY_SEPARATOR . "local.$index.jpg";
		
		$downloadedRemoteURL = $this->buildRemoteURL($partnerId, $entryId, $params);
		$downloadedLocalURL = $this->buildLocalURL($entryId, $params);
		
		clearstatcache();
		if(file_exists($downloadedRemote))
			unlink($downloadedRemote);
		if(file_exists($downloadedLocal))
			unlink($downloadedLocal);

		$status1 = file_put_contents($downloadedRemote, file_get_contents($downloadedRemoteURL));
		$status2 = file_put_contents($downloadedLocal, file_get_contents($downloadedLocalURL));
		
		if (@filesize($downloadedRemote) === false)
			$this->fail("Remote file [$downloadedRemote] is empty");
		
		if (@filesize($downloadedLocal) === false)
			$this->fail("Local file [$downloadedLocal] is empty");
			
		$this->assertGreaterThan(0, $status1, "Remote file [$downloadedRemoteURL] not downloaded");
		$this->assertGreaterThan(0, $status2, "Local file [$downloadedLocalURL] not downloaded");
		
		$this->assertEquals($status1, @filesize($downloadedRemote), "Remote file [$downloadedRemoteURL] does not match the downloaded file size [$status1]");
		$this->assertEquals($status2, @filesize($downloadedLocal), "Local file [$downloadedLocalURL] does not match the downloaded file size [$status2]");
				 
		// check if the file's size are the same (upto a known tolerance)					
		$downloadedRemoteSize = @filesize($downloadedRemote);
		$downloadedLocalSize = @filesize($downloadedLocal);
		$this->assertEquals($downloadedRemoteSize, $downloadedLocalSize, "Files sizes are different downloaded [$downloadedRemoteSize] generated [$downloadedLocalSize]"); 
			
		// check if the image's height and width are the same
		// if images width/height is within tolerance but not identical end test (as success)
		$downloadedRemoteInfo = getimagesize($downloadedRemote);
		$downloadedLocalInfo = getimagesize($downloadedLocal);
		$this->assertEquals($downloadedRemoteInfo[0], $downloadedLocalInfo[0], "Files width is different downloaded [" . $downloadedRemoteInfo[0] . "] generated [" . $downloadedLocalInfo[0] . "]");
		$this->assertEquals($downloadedRemoteInfo[1], $downloadedLocalInfo[1], "Files height is different downloaded [" . $downloadedRemoteInfo[1] . "] generated [" . $downloadedLocalInfo[1] . "]");
			
		// check if images are identical, graphica-wise (upto a given tolerance) 
		$cmd = "{$this->compareCommand} -metric RMSE $downloadedRemote $downloadedLocal diff.$index.png > diff.$index.log 2>&1";		
		$retValue = null;
		$output = null;
		KalturaLog::debug("Execute: $cmd");
		system($cmd, $retValue);
		$output = file_get_contents("diff.$index.log");
		KalturaLog::debug("returned value [$retValue] output: \n$output\n");
		
		if ($retValue != 0)
			$this->fail($output);
		
		if(file_exists($downloadedRemote))
			unlink($downloadedRemote);
		if(file_exists($downloadedLocal))
			unlink($downloadedLocal);					
	}
}

convertImageAccessLogUTest::doTests();