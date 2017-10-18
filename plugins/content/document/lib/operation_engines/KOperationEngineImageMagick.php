<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEngineImageMagick extends KOperationEngineDocument
{
	const PDF_FORMAT = 'PDF document';
	const JPG_FORMAT = 'JPEG image data';
	
	const IMAGES_LIST_XML_NAME = 'imagesList.xml';
	
	const IMAGES_LIST_XML_LABEL_ITEMS = 'items';
	
	const IMAGES_LIST_XML_LABEL_ITEM = 'item';
	
	const IMAGES_LIST_XML_LABEL_NAME = 'name';
	
	const IMAGES_LIST_XML_ATTRIBUTE_COUNT = 'count';
	
	const LEADING_ZEROS_PADDING = '-%03d';
	
	// This is the value that underneath the image is suspected as being a black image
	const BLACK_IMAGE_STD_LIMIT = 50;
	
	// List of supported file types
	private $SUPPORTED_FILE_TYPES = array(
			'PDF document',
	);
	
	// List of errors in case of corrupted file
	private $SUSPECTED_AS_FAILURE = array(
			"/typecheck in --run—",
			"/undefinedresult in --run--",
			"/VMerror in --showpage--GPL",
			"Cannot find a 'startxref'",
			"/ioerror in --.reusablestreamd",
			"/ioerror in --showpage--",
			"/undefined in --run--",
			"Cannot find a %EOF marker",
			"/undefinedresult in --atan--"
	);
	
	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
		KalturaLog::info("cmd [$cmd], outFilePath [$outFilePath]");
	}

	protected function getCmdLine()
	{
		putenv("MAGICK_THREAD_LIMIT=1");
		$exeCmd =  parent::getCmdLine();
		KalturaLog::info("command line: [$exeCmd]");
		return $exeCmd;
	}

	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		if(kFile::fullMkfileDir($this->outFilePath)){
			//outFilePath will be the path to the directory in which the images will be saved.
			$outDirPath = $this->outFilePath;
			//imageMagick decides the format of the output file according to the outFilePath's extension.so the format need to be added.
			$this->outFilePath = $this->outFilePath.DIRECTORY_SEPARATOR.basename($this->outFilePath).self::LEADING_ZEROS_PADDING.'.'.$this->data->flavorParamsOutput->format;
		}
		else
		{
			throw new KOperationEngineException('failed to create ['.$this->outFilePath.'] directory');
		}
		
		$ext = strtolower(pathinfo($inFilePath, PATHINFO_EXTENSION));
		$inputFormat = $this->getInputFormat();
		
		if($inputFormat == self::PDF_FORMAT && $ext != 'pdf' && kFile::linkFile($inFilePath, "$inFilePath.pdf"))
			$inFilePath = "$inFilePath.pdf";
		
		if($inputFormat == self::JPG_FORMAT && $ext != 'jpg' && kFile::linkFile($inFilePath, "$inFilePath.jpg"))
			$inFilePath = "$inFilePath.jpg";
		
		$realInFilePath = realpath($inFilePath);
		// Test input
		// - Test file type
		$errorMsg = $this->checkFileType($realInFilePath, $this->SUPPORTED_FILE_TYPES);
		if(!is_null($errorMsg)){
			$this->data->engineMessage = $errorMsg;
			throw new KOperationEngineException($errorMsg);
		}
		
		// Test password required
		if($this->testPasswordRequired($realInFilePath)) {
			$this->data->engineMessage = "Password required.";
		}
		
		parent::operate($operator, $realInFilePath, $configFilePath);
		
		$imagesList = kFile::dirList($outDirPath,false);
		// Test output
		// - Test black Image
		$identifyExe = KBatchBase::$taskConfig->params->identify;
		$firstImage = $outDirPath . DIRECTORY_SEPARATOR . $imagesList[0];
		$errorMsg = $this->testBlackImage($identifyExe, $firstImage, $errorMsg);
		if(!is_null($errorMsg)) {
			$this->data->engineMessage = $errorMsg;
		}
		
		$imagesListXML = $this->createImagesListXML($imagesList);
	    kFile::setFileContent($outDirPath.DIRECTORY_SEPARATOR.self::IMAGES_LIST_XML_NAME, $imagesListXML->asXML());
	    KalturaLog::info('images list xml ['.$outDirPath.DIRECTORY_SEPARATOR.self::IMAGES_LIST_XML_NAME.'] created');
	    return true;
	}
	
	protected function operationComplete($rc, $output) {
		if($rc != 0) {
			$logOutput = file_get_contents($this->logFilePath);
			foreach($this->SUSPECTED_AS_FAILURE as $possibleFailure) {
				if(strpos($logOutput, $possibleFailure) !== false) {
					$this->data->engineMessage = "Suspected as corrupted file";
					break;
				}
			}
		}
	}
	
	// The returned xml will be stored in the images directory. it than can be downloaded by he user with serveFlavorAction and provide him
	// information about the created images.
	private function createImagesListXML($imagesList){
		sort($imagesList);
		$imagesListXML = new SimpleXMLElement('<'.self::IMAGES_LIST_XML_LABEL_ITEMS.'/>');
		foreach ($imagesList as $image) {
    		$imageNode = $imagesListXML->addChild(self::IMAGES_LIST_XML_LABEL_ITEM);
    		$imageNode->addChild(self::IMAGES_LIST_XML_LABEL_NAME, $image);
		}
		$imagesListXML -> addAttribute(self::IMAGES_LIST_XML_ATTRIBUTE_COUNT, count($imagesList));
		return $imagesListXML;	
	}
	
	private function testPasswordRequired($file) {
		$matches = null;
		$pdfInfo = $this->getPdfInfo($file);
		foreach($pdfInfo as $cur) {
			if(preg_match('/Error: Incorrect password/', $cur, $matches))
				return true;
		}
		return false;
	}
	
	private function testBlackImage($identifyExe, $filePath ) {
		$returnValue = null;
		$output = null;
		$command = $identifyExe . " -verbose '{$filePath}' 2>&1";
		KalturaLog::info("Executing: $command");
		exec($command, $output, $returnValue);
	
		$std = -1;
		$outputString = implode("\n",$output);
	
		if(preg_match_all('/standard deviation: ([\d\.]*)/', $outputString, $matches)) {
			foreach($matches[1] as $std) {
				if(intval($std) < self::BLACK_IMAGE_STD_LIMIT) {
					return "Image is suspected to be black. Score ($std)";
				}
			}
		} else {
			return "Failed to test Image.";
		}
		return null;
	}
}
