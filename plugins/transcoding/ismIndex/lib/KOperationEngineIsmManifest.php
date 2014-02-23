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
		KalturaLog::debug('start ISM Manifest merge');
		
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
		$fileSyncDesc->fileSyncObjectSubType = 3; //".ism";
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
	
	
	private function mergeIsmManifests($srcFileSyncs, $targetIsmcPath)
	{
		$root = null;
		$filePathMap = array();
		foreach ($srcFileSyncs as $srcFileSync) 
		{
			$pair = null;
			if(array_key_exists($srcFileSync->assetId, $filePathMap))
				$pair = $filePathMap[$srcFileSync->assetId];
			else
				$pair = array();
			if($srcFileSync->fileSyncObjectSubType == 3) //ism file
				$pair[0] = $srcFileSync->fileSyncLocalPath;
			else if($srcFileSync->fileSyncObjectSubType == 1 ) //ismv file
				$pair[1] = $srcFileSync->fileSyncLocalPath;
				
			$filePathMap[$srcFileSync->assetId] = $pair;
		}
		
		foreach ($filePathMap as $filePathPair) 
		{			
			list($ismFilePath, $ismvFilePath) = $filePathPair;
			if($ismFilePath)
			{
				$xml = new SimpleXMLElement(file_get_contents($ismFilePath));
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
	
	private function mergeIsmcManifests(array $srcFileSyncs)
	{
		$root = null;
		foreach ($srcFileSyncs as $srcFileSync) 
		{
			if($srcFileSync->fileSyncObjectSubType == 4)
			{
				$xml = new SimpleXMLElement(file_get_contents($srcFileSync->fileSyncLocalPath));
				if(!$root)
  				{
  					$root = $xml;
  				}
				else
				{
			  		for($strIdx=0; $strIdx<count($xml->StreamIndex); $strIdx++) 
	  				{
	   					$this->addQualityLevel($root->StreamIndex[$strIdx], $xml->StreamIndex[$strIdx]->QualityLevel);
	  				}
				} 				
			}
		} 
		if($root)		
 			return $root->asXML(); 		
 		else
 			return null;
	}

	private function addQualityLevel(SimpleXMLElement $dest, SimpleXMLElement $source)
	{
 		$tmp = new SimpleXMLElement($dest->saveXML());
 		unset($dest->c);
 		$this->addXMLElement($dest, $source);
 		$index  = count($tmp->QualityLevel);
 		$dest->QualityLevel[$index]['Index'] = $index;
 		foreach ($tmp->c as $obj)
 		{
  			$this->addXMLElement($dest, $obj);
 		}
 		$dest['QualityLevels'] = $index+1;
	}
	
	public function addXMLElement(SimpleXMLElement $dest, SimpleXMLElement $source)
    {
        $new_dest = $dest->addChild($source->getName(), $source[0]);

		foreach($source->attributes() as $name => $val) {
			$new_dest->addAttribute($name, $val);
		} 
        foreach ($source->children() as $child) {
            $this->addXMLElement($new_dest, $child);
        }
    }
}