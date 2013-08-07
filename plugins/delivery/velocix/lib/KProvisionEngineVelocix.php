<?php
/**
 * Provision Engine to provision new Velocix live stream	
 * 
 * @package plugins.velocix
 * @subpackage lib
 */
class KProvisionEngineVelocix extends KProvisionEngine
{
	private $baseServiceUrl;
	
	private $password;
	
	private $userName;
	
	private $streamName;
	
	const APPLE_HTTP_URLS = 'applehttp_urls';
	const HDS_URLS = 'hds_urls';
	const SL_URLS = 'sl_urls';
	const PLAYBACK = 'playback';
	const PUBLISH = 'publish';
	
	public function __construct($taskConfig)
	{
		if (! KBatchBase::$taskConfig->params->restapi->velocixApiBaseServiceUrl)
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: velocixApiBaseServiceUrl is missing from worker configuration. Cannot provision stream");
		
		$this->baseServiceUrl = KBatchBase::$taskConfig->params->restapi->velocixApiBaseServiceUrl;
	}
	
	/* (non-PHPdoc)
	 * @see KProvisionEngine::getName()
	 */
	public function getName() {
		return get_class($this);
	}

	/* (non-PHPdoc)
	 * @see KProvisionEngine::provide()
	 */
	public function provide(KalturaBatchJob $job, KalturaProvisionJobData $data) 
	{
		if (! KBatchBase::$taskConfig->params->restapi->velocixPlaybackHost)
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: velocixPlaybackHost is missing from worker configuration. Cannot provision stream"); 
		
		if (! KBatchBase::$taskConfig->params->restapi->velocixPublishHost)
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, "Error: velocixPublish is missing from worker configuration. Cannot provision stream");  
		
		$this->password = $data->password;
		$this->userName = $data->userName;
		$this->streamName = $data->streamName;
		
		if (!$this->createVelocixAsset())
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, 'Failed to create Velocix asset', $data);
			
		if (!$this->createAssetProfile($data->provisioningParams))
			return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, 'Failed to create Velocix asset profile', $data);
		
		$data->provisioningParams = $this->updateDataWithUrls($data->provisioningParams);
		return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, 'Succesfully provisioned entry', $data);
		
	}
	
	private function updateDataWithUrls( $provisioningParams){
		$playbackHost = KBatchBase::$taskConfig->params->restapi->velocixPlaybackHost;
		$publishHost = KBatchBase::$taskConfig->params->restapi->velocixPublishHost;
		$hdsPlaybackPrefix = KBatchBase::$taskConfig->params->restapi->velocixHDSPlaybackPrefix;
		foreach ($provisioningParams as $provisioningParam){
			switch ($provisioningParam->key){
				case KalturaPlaybackProtocol::HDS:
					$keyValUrls = new KalturaKeyValue();
					$keyValUrls->key = self::HDS_URLS;
					$urls = array(self::PLAYBACK => 'http://'.$hdsPlaybackPrefix.'/'.$this->streamName.'/hds/'.$this->streamName.'.f4m',
								self::PUBLISH =>'rtmp://'.$publishHost.'/livepkgr/'.$this->streamName.'/%i?adbe-live-event=liveevent');
					$keyValUrls->value= serialize($urls);
					$provisioningParams[] = $keyValUrls;
					break;
				case KalturaPlaybackProtocol::APPLE_HTTP:
					$keyValUrls = new KalturaKeyValue();
					$keyValUrls->key = self::APPLE_HTTP_URLS;
					$urls = array(self::PLAYBACK => 'http://'.$playbackHost.'/'.$this->streamName.'/hls/'.$this->streamName.'.m3u8',
								self::PUBLISH =>'http://'.$publishHost.'/'.$this->streamName.'/hls/'.$this->streamName);
					$keyValUrls->value= serialize($urls);
					$provisioningParams[] = $keyValUrls;
					break;
				case KalturaPlaybackProtocol::SILVER_LIGHT:
					$keyValUrls = new KalturaKeyValue();
					$keyValUrls->key = self::SL_URLS;
					$urls = array(self::PLAYBACK => 'http://'.$playbackHost.'/'.$this->streamName.'/smooth/'.$this->streamName.'.isml/Manifest',
								 self::PUBLISH =>'http://'.$publishHost.'/'.$this->streamName.'/smooth/'.$this->streamName.'.isml');
					$keyValUrls->value= serialize($urls);
					$provisioningParams[] = $keyValUrls;
					break;
			}
		}
		return $provisioningParams;
	}
	
	private function createVelocixAsset(){
		$url = $this->baseServiceUrl . "/vxoa/assets/";
		$data = array(
				'asset_name' => $this->streamName,
				'dest_path' => $this->streamName,
				'asset_type' => 'live'
				);
		$data = json_encode($data);
		$res = $this->doCurl($url, $data);
		KalturaLog::info('Velocix asset creation response:'.$res);
		return strstr($res, '201 Created') ? true :  false;
	}
	
	private function createAssetProfile($provisioningParams){
		$url = $this->baseServiceUrl . "/vxoa/assets/".$this->streamName.'/formats';
		$data = array();
		KalturaLog::debug("configuration: ".print_r($provisioningParams,true));
		foreach ($provisioningParams as $provisioningParam){
			/* @var $provisioningParam KalturaKeyValue */
			if ($provisioningParam->key == KalturaPlaybackProtocol::SILVER_LIGHT)
				$playbackProfile = 'smooth';
			elseif ($provisioningParam->key == KalturaPlaybackProtocol::APPLE_HTTP)
				$playbackProfile = 'hls';
			else
				$playbackProfile = $provisioningParam->key;
			$configuratioArray = array();
			$configuratioArray['profile'] = $playbackProfile;
			$configuratioArray['sources'] = array();
			$urlNum = 1;
			$bitrates = explode(',',$provisioningParam->value);
			$isFirst = true;
			foreach ($bitrates as $bitrate){
				if ($provisioningParam->key == KalturaPlaybackProtocol::SILVER_LIGHT)
					$publishProfile = 'piff';
				elseif ($provisioningParam->key == KalturaPlaybackProtocol::APPLE_HTTP)
					$publishProfile = 'hls';
				else
					$publishProfile = $provisioningParam->key;
				$source = array();
				$source['bitrate'] = $bitrate;
				$source['delete'] = 'null';
				$source['profile'] = $publishProfile;
				//for silver light the first resource url should be the stream name 
				$source['url'] = ($isFirst && $provisioningParam->key == KalturaPlaybackProtocol::SILVER_LIGHT) ? $this->streamName : strval($urlNum++);
				$configuratioArray['sources'][] = $source;
				$isFirst = false;
			}
			$data = json_encode($configuratioArray);
			$data = trim($data,'[]');
			$res = $this->doCurl($url, $data);
			KalturaLog::info('Velocix profile creation response:'.$res);
			if (strstr($res, '201 Created') == false) 
				return false;
		}
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see KProvisionEngine::delete()
	 */
	public function delete(KalturaBatchJob $job, KalturaProvisionJobData $data) 
	{
		$this->password = $data->password;
		$this->userName = $data->userName;
		$url = $this->baseServiceUrl . "/vxoa/assets/".$data->streamName;
		$res = $this->doCurl($url, null, true);
		KalturaLog::info('Velocix asset delete response:'.$res);
		if ( strstr($res, '200 OK') )
			return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, 'Succesfully deleted entry', $data);	
		return new KProvisionEngineResult(KalturaBatchJobStatus::FAILED, 'Failed to delete Velocix asset', $data);
	}
	

	/* (non-PHPdoc)
	 * @see KProvisionEngine::checkProvisionedStream()
	 */
	public function checkProvisionedStream(KalturaBatchJob $job, KalturaProvisionJobData $data) 
	{
		return new KProvisionEngineResult(KalturaBatchJobStatus::FINISHED, "Stream is in status Provisioned");
	}
	
	private function doCurl($url, $data = null, $isDelete = false){
		$ch = curl_init($url);
		if ($isDelete)
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		else
			curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_USERPWD, "$this->userName:$this->password");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json','Accept: application/json'));
		KalturaLog::info("Sent data:".$data);
		if ($data)
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		return curl_exec($ch);
	}

	
}