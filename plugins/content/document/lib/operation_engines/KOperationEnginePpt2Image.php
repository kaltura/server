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
	
	
	protected function createOutputDirectory() {
		if(!kFile::fullMkfileDir($this->outFilePath)){
			KalturaLog::debug('failed to create ['.$this->outFilePath.'] directory');
			throw new KOperationEngineException('failed to create ['.$this->outFilePath.'] directory');
		}
	}
	
	protected function createDirDescriber($outDir, $fileName) {
		$fileList = kFile::dirList($outDir, false);
		$fileListXml = $this->createImagesListXML($fileList);
		kFile::setFileContent($outDir . DIRECTORY_SEPARATOR . $fileName, $fileListXml->asXML());
		KalturaLog::info('file list xml [' .$outDir . DIRECTORY_SEPARATOR . $fileName . '] created');
	}
	
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		$this->createOutputDirectory();
		$realInFilePath = realpath($inFilePath);
		
		parent::operate($operator, $realInFilePath, $configFilePath);
		
		$this->createDirDescriber($this->outFilePath, self::IMAGES_LIST_XML_NAME);
		
	    return true;
	}
	
	// The returned xml will be stored in the images directory. it than can be downloaded by he user with serveFlavorAction and provide him
	// information about the created images.
	private function createImagesListXML($imagesList, $ignoreList){
		sort($imagesList);
		$imagesListXML = new SimpleXMLElement('<'.self::LIST_XML_LABEL_ITEMS.'/>');
		foreach ($imagesList as $image) {
			if($image == "xmlFile.xml")
				continue;
    		$imageNode = $imagesListXML->addChild(self::LIST_XML_LABEL_ITEM);
    		$imageNode->addChild(self::LIST_XML_LABEL_NAME, $image);
		}
		
		$imagesListXML->addAttribute(self::LIST_XML_ATTRIBUTE_METADATA, self::METADATA_XML_NAME);
		$count = count($imagesList);
		$imagesListXML -> addAttribute(self::LIST_XML_ATTRIBUTE_COUNT, $count ? $count - 1 : 0);
		return $imagesListXML;	
	}
}
