<?php

class KOperationEngineIsmManifest extends KSingleOutputOperationEngine
{
	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
	}
		
	/* (non-PHPdoc)
	 * @see KOperationEngine::getCmdLine()
	 */
	protected function getCmdLine() {}

	/*
	 * (non-PHPdoc)
	 * @see KOperationEngine::doOperation()
	 * 
	 * 
	 */
	protected function doOperation()
	{
		if(!$this->data->srcFileSyncs)
			return true;
		
		$ismFilePath = $this->outFilePath.".ism";
		$ismcFilePath = $this->outFilePath.".ismc";
		
		$ismcStr = $this->mergeIsmcManifests($this->data->srcFileSyncs);
		file_put_contents($ismcFilePath, $ismcStr);
		
		$ismStr = $this->mergeIsmManifests($this->data->srcFileSyncs, $ismcFilePath);
		file_put_contents($ismFilePath, $ismStr);
		
		$destFileSyncDescArr = array();
		$fileSyncDesc = new KalturaDestFileSyncDescriptor();
		$fileSyncDesc->fileSyncLocalPath = $ismFilePath;
		$fileSyncDesc->fileSyncObjectSubType = 1; //".ism";
		$destFileSyncDescArr[] = $fileSyncDesc;
		
		$fileSyncDesc = new KalturaDestFileSyncDescriptor();
		$fileSyncDesc->fileSyncLocalPath = $ismcFilePath;
		$fileSyncDesc->fileSyncObjectSubType = 4; //".ismc";
		$destFileSyncDescArr[] = $fileSyncDesc;
		
		$this->data->extraDestFileSyncs  = $destFileSyncDescArr;

		$this->data->destFileSyncLocalPath = null;
		$this->outFilePath = null;
				
		return true;
	}
	
	
	/**
	 * 
	 * @param unknown_type $srcFileSyncs
	 * @param unknown_type $targetIsmcPath
	 * @return NULL
	 */
	private function mergeIsmManifests($srcFileSyncs, $targetIsmcPath)
	{
		$root = null;
		$filePathMap = array();
		foreach ($srcFileSyncs as $srcFileSync) 
		{
			$tuple = array();
			if(array_key_exists($srcFileSync->assetId, $filePathMap))
				$tuple = $filePathMap[$srcFileSync->assetId];

			if($srcFileSync->fileSyncObjectSubType == 3) //ism file
				$tuple[0] = $srcFileSync->fileSyncLocalPath;
			else if($srcFileSync->fileSyncObjectSubType == 1 ) //ismv file
				$tuple[1] = $srcFileSync->fileSyncLocalPath;
			$tuple[2] = $srcFileSync->fileEncryptionKey;
				
			$filePathMap[$srcFileSync->assetId] = $tuple;
		}
		
		foreach ($filePathMap as $filePathTuple)
		{			
			list($ismFilePath, $ismvFilePath, $key) = $filePathTuple;
			if($ismFilePath)
			{
				$str = kEncryptFileUtils::getEncryptedFileContent($ismFilePath, $key, KBatchBase::getIV());
				$xml = new SimpleXMLElement($str);
				if(isset($xml->body->switch->video)) $xml->body->switch->video['src'] = basename($ismvFilePath);
  				if(isset($xml->body->switch->audio)) $xml->body->switch->audio['src'] = basename($ismvFilePath);
  				
  				if(!$root)
  				{
  					$root = $xml;
  				}
  				else 
  				{
   					if(isset($xml->body->switch->video)) $this->addXMLElement($root->body->switch, $xml->body->switch->video);
  					if(isset($xml->body->switch->audio)) $this->addXMLElement($root->body->switch, $xml->body->switch->audio); 					
  				}				
			}
		} 		
		if($root)
		{
	 		$root->head->meta['content'] = basename($targetIsmcPath);
	 		return $root->asXML();
		}
		else
			return null;
	}
	
	/**
	 * 
	 * @param array $srcFileSyncs
	 * @return NULL
	 */
	static private function mergeIsmcManifests(array $srcFileSyncs)
	{
		$root = null;
		foreach ($srcFileSyncs as $srcFileSync) {
			if($srcFileSync->fileSyncObjectSubType == 4) {
				$str = kEncryptFileUtils::getEncryptedFileContent($srcFileSync->fileSyncLocalPath, $srcFileSync->fileEncryptionKey, KBatchBase::getIV());
				$xml = new SimpleXMLElement($str);

				/*
				 * Use the first ISMC as a root. 
				 * The other IMSMC files will be merged with the root ISMC
				 */
				if(!$root) {
  					$root = $xml;
  					continue;
  				}
  				
  				/*
  				 *  To merge new ISMC with 'root' ISMC,
  				 *  if new StreamIndex::Type can be matched with ne of root's stream, then add the QualityLevels
  				 *  otherwise - add it to root as a new stream 
  				 */
		  		foreach($xml->StreamIndex as $xmlStream) {
		  			$found = false;
  					foreach($root->StreamIndex as $rootStream) {
  						if((string)$rootStream['Type']==(string)$xmlStream['Type']){
   							self::addQualityLevel($rootStream, $xmlStream->QualityLevel);
   							$found = true;
   							break;
  						}
  					}
  					if(!$found) {
  						self::addXMLElement($root, $xmlStream);	
  					}
				}
			}
		}
		if($root)		
 			return $root->asXML(); 		
 		else
 			return null;
	}

	/**
	 * 
	 * @param SimpleXMLElement $dest
	 * @param SimpleXMLElement $source
	 */
	static private function addQualityLevel(SimpleXMLElement $dest, SimpleXMLElement $source)
	{
 		$tmp = new SimpleXMLElement($dest->saveXML());
 		unset($dest->c);
 		self::addXMLElement($dest, $source);
 		$index  = count($tmp->QualityLevel);
 		$dest->QualityLevel[$index]['Index'] = $index;
 		foreach ($tmp->c as $obj)
 		{
  			self::addXMLElement($dest, $obj);
 		}
 		$dest['QualityLevels'] = $index+1;
	}
	
	/**
	 * 
	 * @param SimpleXMLElement $dest
	 * @param SimpleXMLElement $source
	 */
	static public function addXMLElement(SimpleXMLElement $dest, SimpleXMLElement $source)
    {
        $new_dest = $dest->addChild($source->getName(), $source[0]);

		foreach($source->attributes() as $name => $val) {
			$new_dest->addAttribute($name, $val);
		} 
        foreach ($source->children() as $child) {
            self::addXMLElement($new_dest, $child);
        }
    }
}