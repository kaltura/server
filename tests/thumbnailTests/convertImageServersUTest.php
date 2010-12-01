<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once 'bootstrap.php';

define("TESTSFILE", "convertImageProductionTests.txt");

/**
 * this class tests two servers thumbnail compatibility,
 * by using two image files (that are identical), one on production server and the other on another server.
 * the same thumbnail convertion will be made on the two image files (on different servers) and the outcome
 * will be compared.
 * the url requests are taken from the convertImageServersTests.txt file
 * @author ori
 *
 */
class convertImageServersUTest extends PHPUnit_Framework_TestCase {
	
	// arrays of URL requests.
	// every element is a different URL request
	private $urlServer1 = array();
	private $urlServer2 = array();
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->retrieveTestList(TESTSFILE);
	}

	/**
	 * retrieve information from tests file
	 * @param unknown_type $testsFile - a path to the tests file. containing all tests in a specific format
	 */
	private function retrieveTestList($testsFile)
	{
		$fileHundler = null;
		if (($fileHundler = fopen($testsFile, "r")) === false)
			die ('unable to read tests file [' . $testsFile . ']');
		fgets($fileHundler);	// discard header line
		while (!feof($fileHundler)) {
			$line = fgets($fileHundler);
			$line = explode("\t", $line);
			$this->urlServer1[] = trim($line[0]) . trim(@$line[2]);
			$this->urlServer2[] = trim($line[1]) . trim(@$line[2]);
		}
	}
	
	/**
	 * test convertImage functions on both servers
	 * the test is done by executing a number of different tests on two different servers (on identical files)
	 */
	public function testConvertImage()
	{
		$sizeTolerance = 1000* 100;	// bytes tolerance, under this limit files will be considered as having the same size
		$PSNRTolerance = 41;		//	PSNR tolerance, under this limit fiesl will be considered as having the same graphical characteristic

		// downloaded files from url's. as convencion server 1 is production and server 2 is another sever
		$downloadedFileServer1 = dirname(__FILE__) . '/Server1.jpg';	
		$downloadedFileServer2 = dirname(__FILE__) . '/Server2.jpg';
		
		for ($i = 0; $i < count($this->urlServer1); $i++)
		{
			$status1 = file_put_contents($downloadedFileServer1, file_get_contents($this->urlServer1[$i]));
			$status2 = file_put_contents($downloadedFileServer2, file_get_contents($this->urlServer2[$i]));
			echo 'comparing image files: [' . $this->urlServer1[$i] . '], [' . $this->urlServer2[$i] . ']' . PHP_EOL;
			
			// check if an image was not produce by any of the servers
			if ($status1 == 0 && $status2 == 0)
			{
				echo $this->urlServer1[$i] . ' did not produce a file' . PHP_EOL;
				echo $this->urlServer2[$i] . ' did not produce a file' . PHP_EOL;
				continue;
			}
			elseif ($status1 == 0)
			{
				echo $this->urlServer1[$i] . ' did not produce a file' . PHP_EOL;
				continue;
			}
			elseif ($status2 == 0)
			{
				echo $this->urlServer2[$i] . ' did not produce a file' . PHP_EOL;
				continue;
			}			 
			
			// check if the file's extensions are identical		
			if (pathinfo($downloadedFileServer1, PATHINFO_EXTENSION) != pathinfo($downloadedFileServer2, PATHINFO_EXTENSION))
			{
				echo 'files extension are not identical' . PHP_EOL;
				continue;
			}

			// check if the file's size are the same (upto a known tolerance)					
			if ((abs(@filesize($downloadedFileServer1) - @filesize($downloadedFileServer2))) > $sizeTolerance)
			{
				echo 'files sizes are not identical' . PHP_EOL;
				echo $this->urlServer1[$i] . ': ' . @filesize($downloadedFileServer1) . PHP_EOL;
				echo $this->urlServer2[$i] . ': ' . @filesize($downloadedFileServer2) . PHP_EOL;
				continue;
			}
		
			// check if the image's height and width are the same
			$server1ImageSize = getimagesize($downloadedFileServer1);
			$server2ImageSize = getimagesize($downloadedFileServer2);
			if ((abs($server1ImageSize[0] - $server2ImageSize[0]) > 5) ||
				(abs($server1ImageSize[1] - $server2ImageSize[1]) > 5))
			{
				echo 'files width and/or height are not identical' , PHP_EOL;
				echo $this->urlServer1[$i] . ': '  . $server1ImageSize[0] . 'x' . $server1ImageSize[1] . PHP_EOL;
				echo $this->urlServer2[$i] . ': '  . $server2ImageSize[0] . 'x' . $server2ImageSize[1] . PHP_EOL;
				continue;
			}
		
			// check if images are identical, graphica-wise (upto a given tolerance) 
			$tmpFile = tempnam(dirname(__FILE__), 'imageComperingTmp');
			$compare = dirname(kConf::get('bin_path_imagemagick')) . '\compare';
			$options = '-metric PSNR';
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
				if (!file_exists($downloadedFileServer1) && !file_exists($downloadedFileServer2))
					echo 'files were not downloaded from urls, the parameters produced no images' . PHP_EOL;
				else
					echo 'unable to perform graphical comparison. beside that images seem identical' . PHP_EOL;
				continue;
			}
			$compareResult = floatval($matches[0]);
			echo 'score is: ' . $compareResult . PHP_EOL;
			
			if ($compareResult > $PSNRTolerance)
			{ 	
				echo "graphical comparison returned with highly un-identical value [$compareResult]" . PHP_EOL;
				continue;
			}
			echo 'comparison complete, files are identical' . PHP_EOL;
		}
		// delete all temporal files
		@unlink($downloadedFileServer1);
		@unlink($downloadedFileServer2);
		@unlink("resultLog.txt");					
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}

}

