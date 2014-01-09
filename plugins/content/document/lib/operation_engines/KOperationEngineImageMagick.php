<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEngineImageMagick extends KSingleOutputOperationEngine
{
	const PDF_FORMAT = 'PDF document';
	const JPG_FORMAT = 'JPEG image data';
	
	const IMAGES_LIST_XML_NAME = 'imagesList.xml';
	
	const IMAGES_LIST_XML_LABEL_ITEMS = 'items';
	
	const IMAGES_LIST_XML_LABEL_ITEM = 'item';
	
	const IMAGES_LIST_XML_LABEL_NAME = 'name';
	
	const IMAGES_LIST_XML_ATTRIBUTE_COUNT = 'count';
	
	const LEADING_ZEROS_PADDING = '-%03d';
	
	// List of supported file types
	private $SUPPORTED_FILE_TYPES = array(
			'PDF document',
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
			KalturaLog::debug('dir ['.$this->outFilePath.'] created');
			//outFilePath will be the path to the directory in which the images will be saved.
			$outDirPath = $this->outFilePath;
			//imageMagick decides the format of the output file according to the outFilePath's extension.so the format need to be added.
			$this->outFilePath = $this->outFilePath.DIRECTORY_SEPARATOR.basename($this->outFilePath).self::LEADING_ZEROS_PADDING.'.'.$this->data->flavorParamsOutput->format;
		}
		else
		{
			KalturaLog::debug('failed to create ['.$this->outFilePath.'] directory');
			throw new KOperationEngineException('failed to create ['.$this->outFilePath.'] directory');
		}
		
		$ext = strtolower(pathinfo($inFilePath, PATHINFO_EXTENSION));
		$inputFormat = $this->getInputFormat();
		
		if($inputFormat == self::PDF_FORMAT && $ext != 'pdf' && kFile::linkFile($inFilePath, "$inFilePath.pdf"))
			$inFilePath = "$inFilePath.pdf";
		
		if($inputFormat == self::JPG_FORMAT && $ext != 'jpg' && kFile::linkFile($inFilePath, "$inFilePath.jpg"))
			$inFilePath = "$inFilePath.jpg";
		
		// Test input
		// - Test file type
		$errorMsg = null;
		if(!$this->checkFileType(realpath($inFilePath),$errorMsg)) {
			$this->message = $errorMsg;
		}
		
		// Test password required
		if($this->testPasswordRequired(realpath($inFilePath))) {
			$this->message = "Password required.";
		}
		
		parent::operate($operator, realpath($inFilePath), $configFilePath);
		
		$imagesList = kFile::dirList($outDirPath,false);
		// Test output
		// - Test black Image
		$identifyExe = KBatchBase::$taskConfig->params->identify;
		$firstImage = $outDirPath . DIRECTORY_SEPARATOR . $imagesList[0];
		if(!$this->testBlackImage($identifyExe, $firstImage, $errorMsg)) {
			$this->message = $errorMsg;
		}
		
		$imagesListXML = $this->createImagesListXML($imagesList);
	    kFile::setFileContent($outDirPath.DIRECTORY_SEPARATOR.self::IMAGES_LIST_XML_NAME, $imagesListXML->asXML());
	    KalturaLog::info('images list xml ['.$outDirPath.DIRECTORY_SEPARATOR.self::IMAGES_LIST_XML_NAME.'] created');
	    return true;
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
	
	private function checkFileType($filePath, &$errorMsg) {
	
		$fileInfo = $this->getFileInfo($filePath);
		$supportedTypes = $this->SUPPORTED_FILE_TYPES;
	
		$isValid = false;
		foreach ($supportedTypes as $validType)
		{
			if (strpos($fileInfo, $validType) !== false)
				return true;
		}
	
		$fileType = explode(':', $fileInfo, 2);
		$fileType = substr(trim($fileType[1]), 0, 30);
		$errorMsg = "invalid file type: {$fileType}";
		return false;
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
	
	private function testBlackImage($identifyExe, $filePath, &$errorMsg) {
		$returnValue = null;
		$output = null;
		$command = $identifyExe . " -verbose '{$filePath}' 2>&1";
		KalturaLog::debug("Executing: $command");
		exec($command, $output, $returnValue);
	
		$std = -1;
		$outputString = implode("\n",$output);
	
		if(preg_match_all('/standard deviation: ([\d\.]*)/', $outputString, $matches)) {
			foreach($matches[1] as $std) {
				if(intval($std) < self::STD_LIMIT) {
					$errorMsg = "Image is suspected to be black. Score ($std)";
					return false;
				}
			}
		} else {
			$errorMsg = "Failed to test Image.";
			return false;
		}
		return true;
	}
	
	private function getFileInfo($filePath)
	{
		$returnValue = null;
		$output = null;
		$command = "file '{$filePath}' 2>&1";
		KalturaLog::debug("Executing: $command");
		exec($command, $output, $returnValue);
		return implode("\n",$output);
	}
	
	private function getPdfInfo($file) {
		$output = null;
		$pdfInfoExe = KBatchBase::$taskConfig->params->pdfInfo;
		$cmd = $pdfInfoExe . " " . realpath($file) . " 2>& 1";
		exec($cmd, $output);
		return $output;
	}
}
