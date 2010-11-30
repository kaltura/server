<?php

require_once 'convertImageTester.php';
require_once 'bootstrap.php';

/**
 * this class tests myFileConverter::convertImage function by running the convertImage function on a number
 * of known image files. the output (converted image file) is then compared to the output reference file.
 * @author ori
 *
 */
class convertImageTest
{
	/**
	 * arrays of input data for tests.
	 * every element is a string file name (including or not including full path)
	 * $sourceFiles[$i] will be tested with respect to $targetFiles[$i] and $outputReferenceFiles[$i]
	 */
	private $sourceFiles = array();				// array of different sorce files
	private $outputReferenceFiles = array();	// array of different output reference files

	/**
	 * set up all needed parameters for the test
	 * @param unknown_type $testsFile - test file path. the file contains the source file and
	 * the output reference file for a specefic test
	 * @param unknown_type $imagesDir - the directory of all the images to be tested
	 */
	public function setUp($testsFile, $imagesDir)
	{
		$fileHundler = null;
		if (($fileHundler = fopen($testsFile, "r")) === false)
			die ('unable to read tests file [' . $testsFile . ']');
		fgets($fileHundler);	// discard form header line
		while (!feof($fileHundler)) {
			$line = fgets($fileHundler);
			$line = explode("\t", $line);
			$this->sourceFiles[] = $imagesDir . '/' . trim($line[0]);
			if (count($line)> 1)
				$this->outputReferenceFiles[] = $imagesDir . '/' . trim($line[1]);
			else $this->outputReferenceFiles[] = null;			
		}
	}

	/**
	 * test convertImage function (myFileConverter::convertImage)
	 * the test is done by executing a number of different tests on different files
	 */
	public function testConvertImage()
	{
		$status = null;
		$tester = null;
		
		// test all source files and compare result to output reference file
		for ($i = 0; $i < count($this->sourceFiles); $i++) {			
			$tester = new convertImageTester($this->sourceFiles[$i], $this->outputReferenceFiles[$i]);

			// extract convertion parameters from $outputReferenceFile and update $tester for those parameters
			if ($this->outputReferenceFiles[$i]) 
			{
				$params = array();
				$tmp = array();
				$tmp = explode("_", basename($this->outputReferenceFiles[$i]));
				// get rid of source file name and extension of file
				array_pop($tmp);
				array_shift($tmp);		
				$j = 0;
				while($j < count($tmp)) {
					$params["$tmp[$j]"] = $tmp[$j + 1];
					$j += 2;
				}
				array_key_exists('width', $params) ? $tester->setWidth($params['width']) :  $tester->setWidth();
				array_key_exists('height', $params) ? $tester->setHeight($params['height']) : $tester->setHeight();
				array_key_exists('cropType', $params) ? $tester->setCropType($params['cropType']) : $tester->setCropType();
				array_key_exists('bGColor', $params) ? $tester->setBGColor($params['bGColor']) : $tester->setBGColor();
				array_key_exists('forceJpeg', $params) ? $tester->setForceJpeg($params['forceJpeg']) : $tester->setForceJpeg();
				array_key_exists('quality', $params) ? $tester->setQuality($params['quality']) : $tester->setQuality();
				array_key_exists('srcX', $params) ? $tester->setSrcX($params['srcX']) : $tester->setSrcX();
				array_key_exists('srcY', $params) ? $tester->setSrcY($params['srcY']) : $tester->setSrcY();
				array_key_exists('srcW', $params) ? $tester->setSrcW($params['srcW']) : $tester->setSrcW();
				array_key_exists('srcH', $params) ? $tester->setSrcH($params['srcH']) : $tester->setSrcH();	
			}	
			
			// excute test and assert 
			$status = $tester->execute();
			if ($status === false)
				echo 'unable to convert [' . $tester->getSourceFile() . '] with parameterrs: ' .
					print_r($tester->getParams());
			assert(true === $status);
			
			// check if output is identical to reference output
			$status = $tester->compareTargetReference();
				if ($status === false)
					echo 'images files: ['  . $tester->getOutputReferenceFile() . '], [' .
						$tester->getTargetFile(). '] are not identical';
			assert(true === $status);
			echo 'convertImage test was successfull on file [' . $tester->getSourceFile() . ']' . PHP_EOL;
			unset($tester);
		}
	}
}

$test = new convertImageTest();
$test->setUp("convertImageTestsFiles.txt", dirname(__FILE__) . '/images');
$test->testConvertImage();


	