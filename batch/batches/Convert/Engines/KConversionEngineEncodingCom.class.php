<?php
/**
 * Encoding.com API: http://www.encoding.com/wdocs/ApiDoc
 * 
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KConversionEngineEncodingCom  extends KJobConversionEngine
{
	const ENCODING_COM = "encoding_com";
	
	private $conversionLog;
	
	public function getName()
	{
		return self::ENCODING_COM;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::ENCODING_COM;
	}
	
	public function getLogData()
	{
		return $this->conversionLog;
	}
	
	public function logConvert($data)
	{
		$this->conversionLog .= "$data\n\n";
	}

	protected function getUserId()
	{
		return $this->engine_config->params->EncodingComUserId;
	}

	protected function getUserKey()
	{
		return $this->engine_config->params->EncodingComUserKey;
	}

	protected function getUrl()
	{
		return $this->engine_config->params->EncodingComUrl;
	}
	
	/* (non-PHPdoc)
	 * @see batches/Convert/Engines/KConversionEngine#convertJob()
	 */
	public function convertJob ( KalturaConvertJobData &$data )
	{
		$sendData = new KEncodingComData();
		
		$sendData->setFormatTurbo('yes');
		
		$sendData->setUserId($this->getUserId());
		$sendData->setUserKey($this->getUserKey());
		
		$sendData->setAction(KEncodingComData::ACTION_ADD_MEDIA);
		$sendData->setSource($data->srcFileSyncRemoteUrl);
		
		switch($data->flavorParamsOutput->videoCodec)
		{
			case KalturaVideoCodec::NONE:
				$sendData->setFormatOutput('mp3');
				//$sendData->setFormatVideoCodec('none');
				break;
				
			case KalturaVideoCodec::VP6:
				$sendData->setFormatOutput('flv');
				$sendData->setFormatVideoCodec('vp6');
				break;
				
			case KalturaVideoCodec::FLV:
				$sendData->setFormatOutput('flv');
				$sendData->setFormatVideoCodec('vp6');
				break;
				
			case KalturaVideoCodec::H263:
				return array(false, "Do not support H263");
				
				$sendData->setFormatOutput('3gp');
				$sendData->setFormatVideoCodec('h263');
				break;
				
			case KalturaVideoCodec::H264:
				$sendData->setFormatOutput('mp4');
				$sendData->setFormatVideoCodec('libx264');
				break;
		}
		
		$sendData->setFormatBitrate($data->flavorParamsOutput->videoBitrate);
		
		if(!$data->flavorParamsOutput->width)
			$data->flavorParamsOutput->width = '0';
					
		if(!$data->flavorParamsOutput->height)
			$data->flavorParamsOutput->height = '0';
			
		$sendData->setFormatSize($data->flavorParamsOutput->width . 'x' . $data->flavorParamsOutput->height);
		$sendData->setFormatKeyFrame($data->flavorParamsOutput->gopSize);
		$sendData->setFormatFramerate($data->flavorParamsOutput->frameRate);
		$sendData->setFormatAudioBitrate($data->flavorParamsOutput->audioBitrate);
		
		$sendData->setFormatCbr("no");
		if($data->flavorParamsOutput->twoPass)
		{
			$sendData->setFormatTwoPass("yes");
		}
		else
		{
			$sendData->setFormatTwoPass("no");
		}

		$err = null;
		$requestXml = $sendData->getXml();
		$responseXml = $this->sendRequest($requestXml, $err);
		
		if(!$responseXml)
			return array(false, $err);
	
		if(preg_match("/\<errors\>(.+)\<\/errors\>/i", $responseXml, $arr))
		{
			$err = isset($arr[1]) ? $arr[1] : $responseXml;
			return array(false, $err);
		}
		
		if(preg_match("/\<mediaid\>(\w*)\<\/mediaid\>/i", $responseXml, $arr))
		{
			$media_id = isset($arr[1]) ? $arr[1] : null;
			if (!$media_id)
				return array(false, "media id was not returned");
				
			$data->remoteMediaId = $media_id;
			return array(true, "Remote Media Id: $media_id");
		}
			
		return array(false, $responseXml);
	}
	
	/**
	 * @param string $requestXml
	 * @param string $err
	 * @return string
	 */
	private function sendRequest($requestXml, &$err)
	{
		KalturaLog::info("sendRequest($requestXml)");

		$url = $this->getUrl();
		
		$this->logConvert("url: $url");
		$this->logConvert("send request:\n$requestXml");
		
		$fields = array(
			"xml" => $requestXml
		);
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$result = curl_exec($ch);
		$this->logConvert("received response:\n$result");
		
		if(!$result)
		{
			$err = curl_error($ch);
			$this->logConvert("curl error: $err");
		}
		
		curl_close($ch);
		
		KalturaLog::info("request results: ($result)");
		return $result;
	}
	
	public function getCmd(){}

}
