<?php

class convertImageTester {
	
	private $sourceFile;
	private $targetFile;
	private $outputReferenceFile;
	private $referenceFile;
	
	private $sizeTol = 1000; 	// number of bytes
	private	$graphicTol = 0.2;	// PSNR
	
	private $params = array();	// wanted parameters of target file
	
	/**
	 * 
	 * update new source file, target file and output reference file 
	 * @param unknown_type $sourceFile
	 * @param unknown_type $targetFile
	 * @param unknown_type $outputReferenceFile
	 * @throws Exception - in case sourec file or output reference file dose not exists
	 */
	public function __construct($sourceFile, $outputReferenceFile)
	{
		if (!file_exists($sourceFile))
			throw new Exception("[$sourceFile] file dose not exists");
		$this->sourceFile = $sourceFile;
		$this->outputReferenceFile = $outputReferenceFile;
		$this->referenceFile = $outputReferenceFile;
		$fileName = explode('.', basename($this->sourceFile));
		$this->targetFile = dirname($this->sourceFile) . "/" . $fileName[0] . 'Target.' . $fileName[1];
		
		// set up default parameters for convertion
		$this->params = array
		(
			'width' => 120,
			'height' => 90,
			'cropType' => 1,
			'bGColor' => 0xffffff,
			'forceJpeg' => 0,
			'quality' => 0,
			'srcX' => 0,
			'srcY' => 0,
			'srcW' => 0,
			'srcH' => 0,
		);
	}
	
	/**
	 * delete the target file that was created during the test
	 */
	public  function __destruct()
	{
		@unlink($this->targetFile);
		if (strcmp($this->referenceFile, $this->outputReferenceFile) !== 0)
			@unlink($this->referenceFile);
	}
	
	public function getSourceFile() { return $this->sourceFile; }
	
	public function getTargetFile() { return $this->targetFile; }
		
	public function getOutputReferenceFile() { return $this->outputReferenceFile; }
	
	public function getParams() { return $this->params; }
	
	public function setWidth($width = 120) { $this->params['width'] = $width; }
	
	public function setHeight($height = 90) { $this->params['height'] = $height; }
	
	public function setCropType ($cropType = 1) { $this->params['cropType'] = $cropType; }
	
	public function setBGColor ($bGColor = 0xffffff) { $this->params['bGColor'] = $bGColor; }
	
	public function setForceJpeg ($forceJpeg = 0) { $this->params['forceJpeg'] = $forceJpeg; }
	
	public function setQuality ($quality = 0) { $this->params['quality'] = $quality; }

	public function setSrcX ($srcX = 0) { $this->params['srcX'] = $srcX; }
	
	public function setSrcY ($srcY = 0) { $this->params['srcY'] = $srcY; }
	
	public function setSrcW ($srcW = 0) { $this->params['srcW'] = $srcW; }
	
	public function setSrcH ($srcH = 0) { $this->params['srcH'] = $srcH; }
	
	public function setGraphicTol($graphicTol) { $this->graphicTol = $graphicTol; }
	
	public function setByteTol($byteTol) { $this->sizeTol = $byteTol; }
	
	public function getGraphicTol() { return $this->graphicTol; }
	
	public function getByteTol() { return $this->sizeTol; }
	
	/**
	 * run an image converting test, according to $sourceFile and wanted parameters.
	 * store the target file after convertion.
	 * a call to this function should take place only after $this->setTargetFile, $this->setSourceFile (succsusfull call)
	 * @return - true if excute succeeded and call was succsusfull, otherwise false
	 */
	public function execute() {		
		@unlink($this->targetFile);					
		$this->targetFile = @myFileConverter::convertImage($this->sourceFile, $this->targetFile, $this->params['width'], $this->params['height'],
			$this->params['cropType'], $this->params['bGColor'], $this->params['forceJpeg'], $this->params['quality'],
			$this->params['srcX'], $this->params['srcY'], $this->params['srcW'], $this->params['srcH']);
		if ($this->targetFile === null)
			return true;
		elseif(!file_exists($this->targetFile))	// wait for convertion to end and produce the target file
			sleep(2);
		if (!file_exists($this->targetFile))
			return false;
		return true; 
	}
	
	/**
	 * compare two image files: $outputFile and $outputReferenceFile.
	 * a call to this method should take place only after a call to $this->setOutputRefererenceFile, $this->execute() (succsusfull call)
	 * @return - true if files are identical (as expected), otherwise false
	 */
	public function compareTargetReference() {
		echo 'comparing image files: [' . $this->targetFile . '], [' . $this->outputReferenceFile . ']' . PHP_EOL;
		
		// check if the converting was not complete
		if ($this->targetFile === null)
		{
			// if $this->sourceFile is not an image file
			if (@exif_imagetype($this->sourceFile) === false || @getimagesize($this->sourceFile) === false)
				return true;
			return false;
		}
		
		// filed to generate reference file
		if (!file_exists($this->referenceFile) || @filesize($this->referenceFile) == 0)
		{
			echo 'reference file [' . $this->outputReferenceFile . '] was not produced' . PHP_EOL;
			return false;
		}
		
		// check if the file's extensions are identical		
		if (strcasecmp(pathinfo($this->targetFile, PATHINFO_EXTENSION), pathinfo($this->referenceFile, PATHINFO_EXTENSION)) !== 0)
		{
			echo 'files extension are not identical' . PHP_EOL;
			return false;
		}

		// check if the file's size are the same (upto a known tolerance)
		if ((abs(@filesize($this->targetFile) - @filesize($this->referenceFile))) > $this->sizeTol)
		{
			echo 'files sizes are not identical' . PHP_EOL;
			echo $this->targetFile . ': ' . @filesize($this->targetFile) . PHP_EOL;
			echo $this->outputReferenceFile . ': ' . @filesize($this->referenceFile) . PHP_EOL;
			return false;
		}
		
		// check if the image's height and width are the same
		$referenceImageSize = getimagesize($this->referenceFile);
		$targetImageSize = getimagesize($this->targetFile);
		if (($targetImageSize[0] !== $referenceImageSize[0]) ||
			($targetImageSize[1] !== $referenceImageSize[1]))
		{
			echo 'files width and/or height are not identical' , PHP_EOL;
			echo $this->targetFile . ': '  . $targetImageSize[0] . 'x' . $targetImageSize[1] . PHP_EOL;
			echo $this->outputReferenceFile . ': '  . $referenceImageSize[0] . 'x' . $referenceImageSize[1] . PHP_EOL;
			return false;
		}
		
		// check if images are identical, graphica-wise (upto a given tolerance) 
		$tmpFile = tempnam(dirname(__FILE__), 'imageComperingTmp');
		$convert = dirname(kConf::get('bin_path_imagemagick')) . '\compare';
		$options = '-metric RMSE';
		$cmd = $convert . ' ' . $options . ' ' . $this->targetFile . ' ' . $this->referenceFile . ' ' . $tmpFile .
			' 2>resultLog.txt';		
		$retValue = null;
		$output = null;
		$output = system($cmd, $retValue);
		$matches = array();
		preg_match('/[0-9]*\.?[0-9]*\)/', file_get_contents('resultLog.txt'), $matches);
		@unlink($tmpFile);			// delete tmp comparing file (used to copmpare the two image files)
		@unlink("resultLog.txt");	// delete tmp log file that was used to retrieve compare return value
		if ($retValue != 0)
		{
			echo 'unable to perform graphical comparison'. PHP_EOL;
			return false;
		}

		$compareResult = $matches[0];
		echo 'score is: ' . floatval($compareResult) . PHP_EOL;
		
		if ($compareResult > $this->graphicTol)
		{ 	
			echo "graphical comparison returned with highly un-identical value [$compareResult]" . PHP_EOL;
			return false;
		}				
		return true; 	
	}
	
	/**
	 * this function exists for enabling url tests.
	 * it will download the file from $this->outputReferenceFile URL
	 * and update $this->outputReferenceFile to the path of the downloaded file.
	 * also, change PSNR and byte tolerances (will be used to compare production image result and new code image result)
	 */
	public function downloadUrlFile()
	{
		$url = $this->outputReferenceFile;
		$fileName = explode('.', basename($this->targetFile));
		$this->referenceFile = dirname($this->targetFile) . "/" . $fileName[0] . 'ProductionDownload.' . $fileName[1];
		file_put_contents($this->referenceFile, file_get_contents($url));		
	}
	
	/**
	 * delete the downloaded fole the was recieved from production (URL)
	 */
	public function deleteDownloadFile()
	{
		@unlink($this->reference);
	}

}

?>