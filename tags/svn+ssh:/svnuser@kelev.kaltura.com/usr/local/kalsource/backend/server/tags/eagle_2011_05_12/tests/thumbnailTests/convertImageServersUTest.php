<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once 'bootstrap.php';

define ( "TESTSFILE", dirname ( __FILE__ ) . "/convertImageServersTests.txt" );
define ("FILE1", "Server1.jpg");
define ("FILE2", "Server2.jpg");

/**
 * this class tests two servers thumbnail compatibility,
 * by using two image files (that are identical), one on production server and the other on another server.
 * the same thumbnail convertion will be made on the two image files (on different servers) and the outcome
 * will be compared.
 * the url requests are taken from the convertImageServersTests.txt file
 * @author ori
 *
 */
class convertImageServersUTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * 
	 * retrieve information from tests file
	 * @return aryay<array> - each element in the array is equivalent to a single test
	 */
	public function providerTestList()
	{
		$provedidData = array();
		$fileHundler = null;
		if (($fileHundler = fopen(TESTSFILE, "r")) === false)
			die ('unable to read tests file [' . TESTSFILE . ']');
			
		fgets($fileHundler);	// discard header line
		while (!feof($fileHundler)) {
			$line = fgets($fileHundler);
			$line = explode("\t", $line);
			$urlServer1 = trim($line[0]) . trim(@$line[2]);
			$urlServer2 = trim($line[1]) . trim(@$line[2]);
			
			// build data for feeding testConvertImage function
			$provedidData[] = array($urlServer1, $urlServer2);
		}
		return $provedidData;
	}
	
	/**
	 * 
	 * test convertImage functions on both servers
	 * the test is done by executing a number of different tests on two different servers (on identical files)
	 * @dataProvider providerTestList
	 */
	public function testConvertImage($urlRequest1, $urlRequest2)
	{		
		$sizeTol = 1000* 1000;	// bytes tolerance, under this limit files will be considered as having the same size
		$graphicTol = 0.2;		//PSNR tolerance, under this limit fiesl will be considered as having the same graphical characteristic
		$pixelTol = 5;			// pixel width / height tolerance
		
		// downloaded files from url's. as convencion server 1 is production and server 2 is another sever
		$downloadedFileServer1 = dirname(__FILE__) . FILE1;	
		$downloadedFileServer2 = dirname(__FILE__) . FILE2;
		@unlink($downloadedFileServer1);
		@unlink($downloadedFileServer2);

		$status1 = file_put_contents($downloadedFileServer1, file_get_contents($urlRequest1));
		$status2 = file_put_contents($downloadedFileServer2, file_get_contents($urlRequest2));
			
		// check if an image was not produce by any of the servers
		if (@getimagesize($downloadedFileServer1) == false && @getimagesize($downloadedFileServer1) === false)
			return;
		$this->assertTrue($status1 != 0, $urlRequest1 . ' - did not produce an image file' . PHP_EOL);
		$this->assertTrue($status2 != 0, $urlRequest1 . ' - did not produce an image file' . PHP_EOL);
		$this->assertFalse(@getimagesize($downloadedFileServer1) === false,
			$urlRequest1 . ' - did not produce an image file' . PHP_EOL);
		$this->assertFalse(@getimagesize($downloadedFileServer2) === false,
			$urlRequest2 . ' - did not produce an image file' . PHP_EOL);	
				 
		// check if the file's extensions are identical		
		$this->assertTrue(pathinfo($downloadedFileServer1, PATHINFO_EXTENSION) == pathinfo($downloadedFileServer2, PATHINFO_EXTENSION), 
			'files extension are not identical' . PHP_EOL);
		
		// check if the file's size are the same (upto a known tolerance)					
		$this->assertTrue((abs(@filesize($downloadedFileServer1) - @filesize($downloadedFileServer2))) < $sizeTol, 
			'files sizes are not identical: ' . PHP_EOL . 
			$urlRequest1 . ': ' . @filesize($downloadedFileServer1) . ' bytes' . PHP_EOL . 
			$urlRequest2 . ': ' . @filesize($downloadedFileServer2) . ' bytes' .PHP_EOL);
		
		// check if the image's height and width are the same
		// if images width/height is within tolerance but not identical end test (as success)
		$server1ImageSize = getimagesize($downloadedFileServer1);
		$server2ImageSize = getimagesize($downloadedFileServer2);
		$this->assertTrue((abs($server1ImageSize[0] - $server2ImageSize[0]) < $pixelTol) &&
							(abs($server1ImageSize[1] - $server2ImageSize[1]) < $pixelTol),
						'files width and/or height are not identical: ' . PHP_EOL .
						$urlRequest1 . ': '  . $server1ImageSize[0] . 'x' . $server1ImageSize[1] . PHP_EOL .
						$urlRequest2 . ': '  . $server2ImageSize[0] . 'x' . $server2ImageSize[1] . PHP_EOL);
		if ($server1ImageSize[0] !== $server2ImageSize[0] || $server1ImageSize[1] !== $server2ImageSize[1])
			return;				
		

		// check if images are identical, graphica-wise (upto a given tolerance) 
		$tmpFile = tempnam(dirname(__FILE__), 'imageComperingTmp');
		$compare = dirname(kConf::get('bin_path_imagemagick')) . '\compare';
		$options = '-metric RMSE';
		$cmd = $compare . ' ' . $options . ' ' . $downloadedFileServer1 . ' ' . $downloadedFileServer2 . ' ' . $tmpFile .
			' 2>resultLog.txt';		
		$retValue = null;
		$output = null;
		$output = system($cmd, $retValue);
		$matches = array();
		preg_match('/[0-9]*\.?[0-9]*\)/', file_get_contents('resultLog.txt'), $matches);
		@unlink($tmpFile);			// delete tmp comparing file (used to copmpare the two image files)
		@unlink("resultLog.txt");	// delete tmp log file that (used to retrieve compare return value)
		
		if ($retValue != 0)
		{
			$this->assertFalse(!file_exists($downloadedFileServer1) && !file_exists($downloadedFileServer2),
				'files were not downloaded from urls, the parameters produced no images' . PHP_EOL);
			$this->assertTrue(false,
				'unable to perform graphical comparison. beside that images seem identical' . PHP_EOL);
		}
		
		$compareResult = floatval($matches[0]);
	//	echo 'score is: ' . $compareResult . PHP_EOL;
		
		$this->assertTrue($compareResult < $graphicTol,
			"graphical comparison returned with highly un-identical value [$compareResult]" . PHP_EOL);
				
		// delete all temporal files
		@unlink($downloadedFileServer1);
		@unlink($downloadedFileServer2);
		@unlink("resultLog.txt");					
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		@unlink("resultLog.txt");
		@unlink(dirname(__FILE__) . FILE1);
		@unlink(dirname(__FILE__) . FILE2);
		parent::tearDown ();
	}

}

