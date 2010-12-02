<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once 'convertImageTester.php';
require_once 'bootstrap.php';

define ( "TESTSFILE", dirname ( __FILE__ ) . "/convertImageTestsFiles.txt" );
define ( "IMAGESDIR", dirname ( __FILE__ ) . '/images' );

/**
 * test case.
 */
class convertImageUTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * 
	 * retrieve information from tests file
	 * @param string $testsFile - a path to the tests file. containing all tests in a specific format
	 * @param string $imagesDir - path to all images directory. needed for tests
	 * @return array<array> - each element in the array is equivalent to a single test
	 */
	public function providerTestList()
	{
		$fileHundler = null;
		$dataProvided = array();
		if (($fileHundler = fopen ( TESTSFILE, "r" )) === false)
			die ('unable to read tests file [' . TESTSFILE . ']' );
			
		fgets ($fileHundler ); // discard form header line
		while (!feof ($fileHundler)) {
			$line = fgets ($fileHundler);
			$line = explode ("\t", $line);
			$sourceFile = IMAGESDIR . '/' . trim ($line[0]);
			if (count($line) > 1)
				$outputReferenceFile = IMAGESDIR . '/' . trim ($line[1]);
			else
				$outputReferenceFile = null;
				
			// build data for feeding testConvertImage function
			$dataProvided[] = array($sourceFile, $outputReferenceFile);
		}
		return $dataProvided;
	}
	
	/**
	 * 
 	 * test convertImage function (myFileConverter::convertImage)
	 * the test is done by executing a number of different tests on different files
	 * @param string $sourceFile
	 * @param string $outputReferenceFile
	 * @dataProvider providerTestList
	 */
	public function testConvertImage($sourceFile, $outputReferenceFile)
	{
		$status = null;
		$tester = null;
		$tester = new convertImageTester($sourceFile, $outputReferenceFile);
		
		// extract convertion parameters from $outputReferenceFile and update $tester for those parameters
		if ($outputReferenceFile)
		{
			$params = array ();
			$tmp = array ();
			$tmp = explode ( "_", basename ($outputReferenceFile));
			// get rid of source file name and extension of file
			array_pop ( $tmp );
			array_shift ( $tmp );
			$j = 0;
			while ( $j < count ( $tmp ) )
			{
				$params ["$tmp[$j]"] = $tmp [$j + 1];
				$j += 2;
			}
			array_key_exists('width', $params) ? $tester->setWidth($params['width']) : $tester->setWidth();
			array_key_exists('height', $params) ? $tester->setHeight($params['height']) : $tester->setHeight();
			array_key_exists('cropType', $params) ? $tester->setCropType($params['cropType']) : $tester->setCropType();
			array_key_exists('bgColor', $params) ? $tester->setBGColor($params['bGColor']) : $tester->setBGColor();
			array_key_exists('forceJpeg', $params) ? $tester->setForceJpeg($params['forceJpeg']) : $tester->setForceJpeg();
			array_key_exists('quality', $params) ? $tester->setQuality($params['quality']) : $tester->setQuality();
			array_key_exists('srcX', $params) ? $tester->setSrcX ($params ['srcX']) : $tester->setSrcX();
			array_key_exists('srcY', $params) ? $tester->setSrcY ($params ['srcY']) : $tester->setSrcY();
			array_key_exists('srcW', $params) ? $tester->setSrcW ($params ['srcW']) : $tester->setSrcW();
			array_key_exists('srcH', $params) ? $tester->setSrcH ($params ['srcH']) : $tester->setSrcH();
		}
		
		// excute test and assert 
		if (($status = $tester->execute()) === false)
			unset($tester);
		$this->assertTrue($status, 'unable to convert [' . $tester->getSourceFile() . '] with parameterrs: ' .
			print_r($tester->getParams(), true));
		
		// check if output is identical to reference output
		$this->assertTrue($status, 'images files: [' . $tester->getOutputReferenceFile() . '], [' .
			$tester->getTargetFile () . '] are not identical');
			
		unset($tester);
	}

}

