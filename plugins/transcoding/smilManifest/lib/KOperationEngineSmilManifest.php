<?php

/**
 * @package plugins.smilManifest
 * @subpackage lib
 */
class KOperationEngineSmilManifest extends KSingleOutputOperationEngine
{
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
		KalturaLog::debug('starting smil manifest operation');
		
		if(!$this->data->srcFileSyncs)
			return true;

		$smilTemplate = $this->getSmilTemplate();
		$xpath = new DOMXPath($smilTemplate);
		$wrapperElement = $xpath->query('/smil/body/switch')->item(0);
		foreach($this->data->srcFileSyncs as $srcFileSync)
		{
			/** @var KalturaSourceFileSyncDescriptor $srcFileSync */
			$fileName = pathinfo($srcFileSync->actualFileSyncLocalPath, PATHINFO_BASENAME);
			$bitrate = $this->getBitrateForAsset($srcFileSync->assetId);
			$this->addSmilVideo($wrapperElement, $fileName, $bitrate);
		}

		$smilFilePath = $this->outFilePath.".smil";
		$smilData = $smilTemplate->saveXML();
		file_put_contents($smilFilePath, $smilData);

		$destFileSyncDescArr = array();
		$fileSyncDesc = new KalturaDestFileSyncDescriptor();
		$fileSyncDesc->fileSyncLocalPath = $smilFilePath;
		$fileSyncDesc->fileSyncObjectSubType = 5; //".smil";
		$destFileSyncDescArr[] = $fileSyncDesc;

		$this->data->extraDestFileSyncs  = $destFileSyncDescArr;

		$this->data->destFileSyncLocalPath = null;
		$this->outFilePath = null;

		return true;
	}

	protected function addSmilVideo(DOMElement $wrapperElement, $fileName, $bitrate)
	{
		$videoElement = $wrapperElement->ownerDocument->createElement('video');
		$videoElement->setAttribute('src', $fileName);
		$videoElement->setAttribute('system-bitrate', $bitrate * 1024);
		$wrapperElement->appendChild($videoElement);
	}

	protected function getSmilTemplate()
	{
		$xmlData = '<smil>
						<head>
						</head>
						<body>
							<switch>
							</switch>
						</body>
					</smil>
					';
		$doc = new DOMDocument();
		$doc->loadXML($xmlData);
		return $doc;
	}

	protected function getBitrateForAsset($assetId)
	{
		if (!$this->data->pluginData)
			return null;

		foreach($this->data->pluginData as $pluginData)
		{
			/** @var KalturaKeyValue $pluginData */
			if ($pluginData->key == 'asset_'.$assetId.'_bitrate')
				return $pluginData->value;
		}

		return null;
	}
}