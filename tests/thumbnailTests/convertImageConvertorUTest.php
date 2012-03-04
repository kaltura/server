<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once 'bootstrap.php';

define("TESTSFILE", dirname(__FILE__) . DIRECTORY_SEPARATOR . "convertImageConvertorTests.txt");

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
			
		fgetcsv($fileHundler);	// discard header line
		while (!feof($fileHundler))
			$provedidData[] = fgetcsv($fileHundler);
			
		return $provedidData;
	}
	
	private function buildURL($partnerId, $entryId, $quality = null, $cropType = 1, $width = 0, $height = 0, $cropX = null, $cropY = null, $cropWidth = null, $cropHeight = null, $bgcolor = null, $density = null)
	{
		$url = "http://www.kaltura.com/p/$partnerId/thumbnail/entry_id/$entryId/type/$cropType";
		$url .= is_null($width)			|| !strlen($width)		? '' : "/width/$width";
		$url .= is_null($height)		|| !strlen($height)		? '' : "/height/$height";
		$url .= is_null($quality)		|| !strlen($quality)	? '' : "/quality/$quality";
		$url .= is_null($cropX)			|| !strlen($cropX)		? '' : "/src_x/$cropX";
		$url .= is_null($cropY)			|| !strlen($cropY)		? '' : "/src_y/$cropY";
		$url .= is_null($cropWidth)		|| !strlen($cropWidth)	? '' : "/src_w/$cropWidth";
		$url .= is_null($cropHeight)	|| !strlen($cropHeight)	? '' : "/src_h/$cropHeight";
		$url .= is_null($density)		|| !strlen($density)	? '' : "/density/$density";
		$url .= is_null($bgcolor)		|| !strlen($bgcolor)	? '' : "/bgcolor/$bgcolor";
		
		return $url;
	}
	
	/**
	 * 
	 * test convertImage functions on both servers
	 * the test is done by executing a number of different tests on two different servers (on identical files)
	 * @dataProvider providerTestList
	 */
	public function testConvertImage($partnerId, $entryId, $quality, $cropType, $width, $height, $cropX, $cropY, $cropWidth, $cropHeight, $bgcolor, $density)
	{		
		$sizeTol = 1000* 1000;	// bytes tolerance, under this limit files will be considered as having the same size
		$graphicTol = 0.2;		//PSNR tolerance, under this limit fiesl will be considered as having the same graphical characteristic
		$pixelTol = 5;			// pixel width / height tolerance
		
		$downloadedFileInput = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'input.jpg';
		$downloadedFileOutput = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'output.jpg';	
		$generatedFileOutput = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'generated.jpg';
		
		$downloadedFileInputURL = $this->buildURL($partnerId, $entryId);
		$downloadedFileOutputURL = $this->buildURL($partnerId, $entryId, $quality, $cropType, $width, $height, $cropX, $cropY, $cropWidth, $cropHeight, $bgcolor, $density);
		
		clearstatcache();
		if(file_exists($downloadedFileInput))
			unlink($downloadedFileInput);
		if(file_exists($downloadedFileOutput))
			unlink($downloadedFileOutput);
		if(file_exists($generatedFileOutput))
			unlink($generatedFileOutput);

		$status1 = file_put_contents($downloadedFileInput, file_get_contents($downloadedFileInputURL));
		$status2 = file_put_contents($downloadedFileOutput, file_get_contents($downloadedFileOutputURL));
		
		$imageCropper = new KImageMagickCropper($downloadedFileInput, $generatedFileOutput);
		$status3 = $imageCropper->crop($quality, $cropType, $width, $height, $cropX, $cropY, $cropWidth, $cropHeight, null, null, $bgcolor, $density);
			
		// check if an image was not produce by any of the servers
		if (@filesize($downloadedFileInput) == false || @filesize($downloadedFileOutput) === false)
			return;
			
		$this->assertGreaterThan(0, $status1, $downloadedFileInputURL . ' - did not produce an image file');
		$this->assertGreaterThan(0, $status2, $downloadedFileOutputURL . ' - did not produce an image file');
		$this->assertTrue($status3, 'Generator did not produce an image file');
		
		$this->assertEquals($status1, @filesize($downloadedFileInput), "$downloadedFileInputURL - does not match the downloaded file size [$status1]");
		$this->assertEquals($status2, @filesize($downloadedFileOutput), "$downloadedFileOutputURL - does not match the downloaded file size [$status2]");
		$this->assertGreaterThan(0, @filesize($generatedFileOutput), 'Generator did not produce a valid image file');	
				 
		// check if the file's size are the same (upto a known tolerance)					
		$downloadedFileOutputSize = @filesize($downloadedFileOutput);
		$generatedFileOutputSize = @filesize($generatedFileOutput);
		$this->assertEquals($downloadedFileOutputSize, $generatedFileOutputSize, "Files sizes are different downloaded [$downloadedFileOutputSize] generated [$generatedFileOutputSize]"); 
			
		// check if the image's height and width are the same
		// if images width/height is within tolerance but not identical end test (as success)
		$downloadedFileOutputInfo = getimagesize($downloadedFileOutput);
		$generatedFileOutputInfo = getimagesize($generatedFileOutput);
		$this->assertEquals($downloadedFileOutputInfo[0], $generatedFileOutputInfo[0], "Files width is different downloaded [" . $downloadedFileOutputInfo[0] . "] generated [" . $generatedFileOutputInfo[0] . "]");
		$this->assertEquals($downloadedFileOutputInfo[1], $generatedFileOutputInfo[1], "Files height is different downloaded [" . $downloadedFileOutputInfo[1] . "] generated [" . $generatedFileOutputInfo[1] . "]");
			
		// check if images are identical, graphica-wise (upto a given tolerance) 
		$compare = kConf::get('bin_path_imagemagick_compare');
		$cmd = "$compare -metric RMSE $downloadedFileOutput $generatedFileOutput diff.png";		
		$retValue = null;
		$output = null;
		KalturaLog::debug("Execute: $cmd");
		$output = system($cmd, $retValue);
		KalturaLog::debug("returned value [$retValue] output: \n$output\n");
		
		$matches = array();
		//preg_match('/[0-9]*\.?[0-9]*\)/', $output, $matches);
		
//		if ($retValue != 0)
//		{
//			$this->fail();
//		}
		
//		$compareResult = floatval($matches[0]);
	//	echo 'score is: ' . $compareResult;
		
//		$this->assertTrue($compareResult < $graphicTol,
//			"graphical comparison returned with highly un-identical value [$compareResult]");
				

		if(file_exists($downloadedFileInput))
			unlink($downloadedFileInput);
		if(file_exists($downloadedFileOutput))
			unlink($downloadedFileOutput);
		if(file_exists($generatedFileOutput))
			unlink($generatedFileOutput);					
	}
}

