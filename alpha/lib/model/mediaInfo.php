<?php

/**
 * Subclass for representing a row from the 'media_info' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class mediaInfo extends BasemediaInfo
{
	const MEDIA_INFO_BIT_RATE_MODE_CBR = 1;
	const MEDIA_INFO_BIT_RATE_MODE_VBR = 2;
	 
	public function getCacheInvalidationKeys()
	{
		return array("mediaInfo:flavorAssetId=".strtolower($this->getFlavorAssetId()));
	}
	
	public function setIsFastStart($v)	{$this->putInCustomData('IsFastStart', $v);}
	public function getIsFastStart()	{return $this->getFromCustomData('IsFastStart', null, 1);}
	
	public function setContentStreams($v)	{$this->putInCustomData('ContentStreams', $v);}
	public function getContentStreams()	{return $this->getFromCustomData('ContentStreams', null, null);}
	
	public function setComplexityValue($v)	{$this->putInCustomData('ComplexityValue', $v);}
	public function getComplexityValue()	{return $this->getFromCustomData('ComplexityValue', null, null);}
	
	public function setMaxGOP($v)	{$this->putInCustomData('MaxGOP', $v);}
	public function getMaxGOP()	{return $this->getFromCustomData('MaxGOP', null, null);}
	
	public function setMatrixCoefficients($v)	{$this->putInCustomData('MatrixCoefficients', $v);}
	public function getMatrixCoefficients()	{return $this->getFromCustomData('MatrixCoefficients', null, null);}

	public function setColorTransfer($v)	{$this->putInCustomData('ColorTransfer', $v);}
	public function getColorTransfer()	{return $this->getFromCustomData('ColorTransfer', null, null);}

	public function setColorPrimaries($v)	{$this->putInCustomData('ColorPrimaries', $v);}
	public function getColorPrimaries()	{return $this->getFromCustomData('ColorPrimaries', null, null);}

	public function setPixelFormat($v)	{$this->putInCustomData('PixelFormat', $v);}
	public function getPixelFormat()	{return $this->getFromCustomData('PixelFormat', null, null);}

	public function setColorSpace($v)	{$this->putInCustomData('ColorSpace', $v);}
	public function getColorSpace()	{return $this->getFromCustomData('ColorSpace', null, null);}

	public function setChromaSubsampling($v)	{$this->putInCustomData('ChromaSubsampling', $v);}
	public function getChromaSubsampling()	{return $this->getFromCustomData('ChromaSubsampling', null, null);}

	public function setBitsDepth($v)	{$this->putInCustomData('BitsDepth', $v);}
	public function getBitsDepth()	{return $this->getFromCustomData('BitsDepth', null, null);}
	
	public function setSpeechDetected($v)	{$this->putInCustomData('speechDetected', $v);}
	public function getSpeechDetected()	{return $this->getFromCustomData('speechDetected', null, false);}

	public function setRawData($v)
	{
		$saveRawDataAllowedPartners = kConf::get("save_media_info_raw_data_partners", 'local', array());
		if(!count($saveRawDataAllowedPartners))
			return parent::setRawData($v);
	
		$flavorAsset = assetPeer::retrieveById($this->getFlavorAssetId());
		if(!$flavorAsset)
			return parent::setRawData($v);
	
		if(in_array($flavorAsset->getPartnerId(), $saveRawDataAllowedPartners))
			return parent::setRawData($v);
	
		return;
	}
	
	public function getRawDataXml()
	{
		$rawData = $this->getRawData();
		$tokenizer = new KStringTokenizer ( $rawData, "\t\n" );
	
		$rawDataXml = new DOMDocument();
		$rootNode = $rawDataXml->createElement("RawData");
		$root = $rawDataXml->appendChild($rootNode);
		while($tokenizer->hasMoreTokens())
		{
			$rawDataLine = trim($tokenizer->nextToken());
			if(!$rawDataLine)
				continue;
								
			if(strpos($rawDataLine, ":") === false)
			{
				$key = $rawDataLine;
				$value = "";
			}
			else
			{
				list($key, $value) = explode(":", $rawDataLine, 2);
			}
			$key = str_replace(" ", "",$key);
			$key = preg_replace('/[^A-Za-z0-9]/', '_', $key);
			
			if (!$value)
			{
				$parentNode = $rawDataXml->createElement($key);
				$root->appendChild($parentNode);
			}
			else
			{
				$value = trim($value);
				$node = $rawDataXml->createElement($key);
				$value = $rawDataXml->createTextNode (htmlspecialchars($value));
				$node->appendChild($value);
				$parentNode->appendChild($node);
			}
		}
		
		return $rawDataXml->saveXML();
	}

	/**
	 * @return bool
	 */
	public function isContainVideo()
	{
		if ($this->getVideoFormat() || $this->getVideoCodecId() || $this->getVideoDuration()
				|| $this->getVideoBitRate())
			return true;
		return false;
	}

	/**
	 * @return bool
	 */
	public function isContainAudio()
	{
		if ($this->getAudioFormat() || $this->getAudioCodecId() || $this->getAudioDuration()
				|| $this->getAudioBitRate())
			return true;
		return false;
	}
}
