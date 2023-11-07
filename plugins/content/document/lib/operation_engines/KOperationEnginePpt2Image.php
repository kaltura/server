<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEnginePpt2Image extends KOperationEngineDocument
{
	const IMAGES_LIST_XML_NAME = 'imagesList.xml';
	const METADATA_XML_NAME = 'metadata.xml';
	
	const LIST_XML_LABEL_ITEMS = 'items';
	const LIST_XML_LABEL_ITEM = 'item';
	const LIST_XML_LABEL_NAME = 'name';
	const LIST_XML_ATTRIBUTE_METADATA = 'metadata';
	const LIST_XML_ATTRIBUTE_COUNT = 'count';
	const LIST_XML_ATTRIBUTE_INDEX = 'index';
	
	
	protected function createOutputDirectory() {
		if(!kFile::fullMkfileDir($this->outFilePath)){
			throw new KOperationEngineException('failed to create ['.$this->outFilePath.'] directory');
		}
	}
	
	protected function createDirDescriber($outDir, $fileName, $key) {
		$fileList = kFile::dirList($outDir, false);
		$fileListXml = $this->createImagesListXML($fileList, $outDir, $key);
		kFile::setFileContent($outDir . $fileName, $fileListXml->asXML());
		KalturaLog::info('file list xml [' . $outDir . $fileName . '] created');
	}
	
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		$this->createOutputDirectory();
		$realInFilePath = realpath($inFilePath);
		$key = $this->getFileNameEncryptKey($operator);
		$outDirPath = $this->outFilePath . DIRECTORY_SEPARATOR;
		
		parent::operate($operator, $realInFilePath, $configFilePath);
		
		$this->createDirDescriber($outDirPath, self::IMAGES_LIST_XML_NAME, $key);

		parent::jsonFormat(array('pageList' => self::IMAGES_LIST_XML_NAME, 'metadata' => self::METADATA_XML_NAME), $outDirPath);
		self::encryptFileName($outDirPath, self::IMAGES_LIST_XML_NAME, $key);
		self::encryptFileName($outDirPath, self::METADATA_XML_NAME, $key);
		self::encryptFileName($outDirPath, self::DOC_METADATA_JSON_NAME, $key);
	    return true;
	}
	
	// The returned xml will be stored in the images directory. it than can be downloaded by the user with serveFlavorAction and provide him
	// information about the created images.
	private function createImagesListXML($imagesList, $outDir, $key){
		sort($imagesList);
		$i = 1;
		$imagesListXML = new SimpleXMLElement('<'.self::LIST_XML_LABEL_ITEMS.'/>');
		foreach ($imagesList as $image) {
			if($image == self::METADATA_XML_NAME)
				continue;
    		$imageNode = $imagesListXML->addChild(self::LIST_XML_LABEL_ITEM);
    		$imageNode->addAttribute(self::LIST_XML_ATTRIBUTE_INDEX, $i++);
    		$imageNode->addChild(self::LIST_XML_LABEL_NAME, self::encryptFileName($outDir, $image, $key));
		}
		
		$imagesListXML->addAttribute(self::LIST_XML_ATTRIBUTE_METADATA, self::METADATA_XML_NAME);
		$count = count($imagesList);
		$imagesListXML -> addAttribute(self::LIST_XML_ATTRIBUTE_COUNT, $count ? $count - 1 : 0);
		return $imagesListXML;	
	}
}
