<?php

require_once 'PHPUnit\Framework\TestCase.php';

require_once 'convertImageTester.php';
require_once 'bootstrap.php';

define ("TESTSFILE", dirname ( __FILE__ ) . "/convertImageProductionTests.txt");
define("IMAGESDIR", dirname(__FILE__) . '/images');

/**
 * this class tests the compatibility of a new code with the code  residinf in producion.
 * the myconverter::convertImage function will run on a known set of image files and convert them
 * using some parameters. the same will be done by calling to URL request (production side). after that
 * the two outputs will be compared.
 * @author ori
 */
class convertImageProductionUTest extends PHPUnit_Framework_TestCase
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
		fgets($fileHundler);	// discard form header line
		while (!feof($fileHundler)) {
			$line = fgets($fileHundler);
			$line = explode("\t", $line);
			$sourceFile = IMAGESDIR . '/' . trim($line[0]);
			$outputReferenceFile = trim($line[1]);
			
			// build data for feeding testConvertImage function
			$provedidData[] = array($sourceFile, $outputReferenceFile);
		}
		return $provedidData;
	}
	
	/**
	 * 
	 * test convertImage function (myFileConverter::convertImage)
	 * the test is done by executing a number of different tests on different files and comparing result
	 * to the produvtion servert results
	 * @dataProvider providerTestList
	 */
	public function testConvertImage($sourceFile, $outputReferenceFile)
	{
		$status = null;
		$tester = null;
		$tester = new convertImageTester($sourceFile, $outputReferenceFile);

		// extract convertion parameters from $outputReferenceFile and update $tester for those parameters
		$params = array();
		$tmp = array();
		$tmp = explode("/", $outputReferenceFile);
		// get rid of source file name and extension of file
		for ($j = 0; $j < 8 ; $j++)
			array_shift($tmp);
		array_pop($tmp);				
		$j = 0;
		while($j < count($tmp)) {
			$params["$tmp[$j]"] = $tmp[$j + 1];
			$j += 2;
		}
		array_key_exists('width', $params) ? $tester->setWidth($params['width']) :  $tester->setWidth();
		array_key_exists('height', $params) ? $tester->setHeight($params['height']) : $tester->setHeight();
		array_key_exists('type', $params) ? $tester->setCropType($params['type']) : $tester->setCropType();
		array_key_exists('bGColor', $params) ? $tester->setBGColor(hexdec($params['bGColor'])) : $tester->setBGColor();
		array_key_exists('quality', $params) ? $tester->setQuality($params['quality']) : $tester->setQuality();
		array_key_exists('src_x', $params) ? $tester->setSrcX($params['src_x']) : $tester->setSrcX();
		array_key_exists('src_y', $params) ? $tester->setSrcY($params['src_y']) : $tester->setSrcY();
		array_key_exists('src_w', $params) ? $tester->setSrcW($params['src_w']) : $tester->setSrcW();
		array_key_exists('src_h', $params) ? $tester->setSrcH($params['src_h']) : $tester->setSrcH();	
			
		// excute test and assert
		
		// excute test and assert 
		if (($status = $tester->execute()) === false)
			unset($tester);
		$this->assertTrue($status, 'unable to convert [' . $tester->getSourceFile() . '] with parameterrs: ' .
			print_r($tester->getParams(), true));
			
		// download from production the converted image (thumbnail) and
		$tester->downloadUrlFile();
		$tester->setGraphicTol(0.25);
		$tester->setByteTol($tester->getByteTol() * 100);
		
		// check if output is identical to reference output
		$this->assertFalse($tester->checkConvertionComplete(),
			'reference file [' . $tester->getOutputReferenceFile() . '] was not produced' . PHP_EOL);
		
		$this->assertTrue($tester->checkExtensions(),
			'files extension are not identical' . PHP_EOL);
		$this->assertTrue($tester->checkSize(),
			'files sizes are not identical');
		$this->assertTrue($tester->checkWidthHeigth(),
			'files width / height are not identical'); 
		$status = $tester->checkGraphicSimilarity();
		$this->assertTrue($status < 1,
			'unable to perform graphical comparison'. PHP_EOL);
		$this->assertTrue($tester->getGraphicTol() > $status,
			"graphical comparison returned with highly un-identical value [$status]" . PHP_EOL);
		
	//	$status = $tester->compareTargetReference();
	//	$tester->deleteDownloadFile();
		
	//	$this->assertTrue($status, 'images files: [' . $tester->getOutputReferenceFile() . '], [' .
	//		$tester->getTargetFile () . '] are not identical');

		unset($tester);
	}

}

