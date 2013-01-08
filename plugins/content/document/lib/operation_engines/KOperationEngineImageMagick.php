<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEngineImageMagick extends KSingleOutputOperationEngine
{
	const PDF_FORMAT = 'PDF document';
	const JPG_FORMAT = 'JPEG image data';

	/**
	 * @var KalturaPdfFlavorParamsOutput
	 */
	private $flavorParamsOutput;
	
	const IMAGES_LIST_XML_NAME = 'imagesList.xml';
	
	const IMAGES_LIST_XML_LABEL_ITEMS = 'items';
	
	const IMAGES_LIST_XML_LABEL_ITEM = 'item';
	
	const IMAGES_LIST_XML_LABEL_NAME = 'name';
	
	const IMAGES_LIST_XML_ATTRIBUTE_COUNT = 'count';
	
	const LEADING_ZEROS_PADDING = '-%03d';
	
	
	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data, KalturaClient $client)
	{
		parent::configure($taskConfig, $data, $client);
		$this->flavorParamsOutput = $data->flavorParamsOutput;
	}
	
	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
		KalturaLog::info("cmd [$cmd], outFilePath [$outFilePath]");
	}

	protected function getCmdLine()
	{
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
			$this->outFilePath = $this->outFilePath.DIRECTORY_SEPARATOR.basename($this->outFilePath).self::LEADING_ZEROS_PADDING.'.'.$this->flavorParamsOutput->format;
		}
		else
		{
			KalturaLog::debug('failed to create ['.$this->outFilePath.'] directory');
			throw new KOperationEngineException('failed to create ['.$this->outFilePath.'] directory');
		}
		
		$ext = strtolower(pathinfo($inFilePath, PATHINFO_EXTENSION));
		$inputFormat = $this->getInputFormat();
		
		if($inputFormat == self::PDF_FORMAT && $ext != 'pdf' && kFile::moveFile($inFilePath, "$inFilePath.pdf"))
			$inFilePath = "$inFilePath.pdf";
		
		if($inputFormat == self::JPG_FORMAT && $ext != 'jpg' && kFile::moveFile($inFilePath, "$inFilePath.jpg"))
			$inFilePath = "$inFilePath.jpg";
			
		parent::operate($operator, $inFilePath, $configFilePath);
		$imagesListXML = $this->createImagesListXML($outDirPath);
	    kFile::setFileContent($outDirPath.DIRECTORY_SEPARATOR.self::IMAGES_LIST_XML_NAME, $imagesListXML->asXML());
	    kalturalog::info('images list xml ['.$outDirPath.DIRECTORY_SEPARATOR.self::IMAGES_LIST_XML_NAME.'] created');
	}
	

	// The returned xml will be stored in the images directory. it than can be downloaded by he user with serveFlavorAction and provide him
	// information about the created images.
	private function createImagesListXML($outDirPath){
		$imagesList = kFile::dirList($outDirPath,false);
		sort($imagesList);
		$imagesListXML = new SimpleXMLElement('<'.self::IMAGES_LIST_XML_LABEL_ITEMS.'/>');
		foreach ($imagesList as $image) {
    		$imageNode = $imagesListXML->addChild(self::IMAGES_LIST_XML_LABEL_ITEM);
    		$imageNode->addChild(self::IMAGES_LIST_XML_LABEL_NAME, $image);
		}
		$imagesListXML -> addAttribute(self::IMAGES_LIST_XML_ATTRIBUTE_COUNT, count($imagesList));
		return $imagesListXML;	
	}
	
}