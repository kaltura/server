<?php

/**
 * Encoding.com API: http://www.encoding.com/wdocs/ApiDoc
 * 
 * @package Scheduler
 * @subpackage Conversion
 */
class KOperationEngineEncodingCom  extends KOperationEngine
{
	/**
	 * @var string
	 */
	protected $userId;
	
	/**
	 * @var string
	 */
	protected $userKey;
	
	/**
	 * @var string
	 */
	protected $url;

	protected function __construct($userId, $userKey, $url)
	{
		parent::__construct();
		
		$this->userId = $userId;
		$this->userKey = $userKey;
		$this->url = $url;
	}
	
	/* (non-PHPdoc)
	 * @see batches/Convert/OperationEngines/KOperationEngine#doOperation()
	 */
	protected function doOperation()
	{
		$sendData = new KEncodingComData();
		
		$sendData->setFormatTurbo('yes');
		
		$sendData->setUserId($this->userId);
		$sendData->setUserKey($this->userKey);
		
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
			throw new KOperationEngineException($err);
			
		if(preg_match('/\<errors\>(.+)\<\/errors\>/i', $responseXml, $arr))
		{
			$err = isset($arr[1]) ? $arr[1] : $responseXml;
			throw new KOperationEngineException($err);
		}
		
		if(!preg_match('/\<mediaid\>(\w*)\<\/mediaid\>/i', $responseXml, $arr))
			throw new KOperationEngineException($responseXml);
			
		$media_id = isset($arr[1]) ? $arr[1] : null;
		if (!$media_id)
			throw new KOperationEngineException("media id was not returned");
			
		$data->remoteMediaId = $media_id;
		$this->message = "Remote Media Id: $media_id";
	}
	
	/**
	 * @param string $requestXml
	 * @param string $err
	 * @return string
	 */
	private function sendRequest($requestXml, &$err)
	{
		$this->addToLogFile($requestXml);

		$url = $this->url;
		
		$this->addToLogFile("url: $url");
		$this->addToLogFile("send request:\n$requestXml");
		
		$fields = array(
			"xml" => $requestXml
		);
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$result = curl_exec($ch);
		$this->addToLogFile("received response:\n$result");
		
		if(!$result)
		{
			$err = curl_error($ch);
			$this->addToLogFile("curl error: $err");
		}
		
		curl_close($ch);
		
		$this->addToLogFile("request results: ($result)");
		return $result;
	}
	
	protected function getCmdLine(){}
}
