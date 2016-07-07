<?php
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage lib
 */
class PushToNewsDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineUpdate,
	IDistributionEngineDelete
{
	const CERT_KEY_FILE_NAME = "cert.pem";
	const TEMP_DIRECTORY = 'push_to_news_distribution';

	/**
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		return $this->submitOrUpdate($data);
	}
	
	/**
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		return $this->submitOrUpdate($data);
	}
	
	public function submitOrUpdate(KalturaDistributionJobData $data)
	{
		$this->validateJobObjects($data);

		$distributionProfile = $data->distributionProfile;
		$host = $distributionProfile->host;
		$port = $distributionProfile->port;
		$password = $distributionProfile->password;
		$basePath = $distributionProfile->basePath;
		
		$url = $host;
		$url .= $port ? ":$port" : "";
		$url .= "/" . $basePath;

		KalturaLog::debug("url - $url");

		$certLocation = $this->getFileLocationForCertificateKey($distributionProfile->id, $distributionProfile->certificateKey, self::CERT_KEY_FILE_NAME);

		$providerData = $data->providerData;
		$objectsForDistribution = $providerData->objectsForDistribution;

		foreach($objectsForDistribution as $distributionObject)
		{
			$success = $this->distributeContent($distributionObject, $url, $certFileLocation, $password);
			if(!$success)
				return false;
		}

		return true;
	}
	
	/**
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		$this->validateJobObjects($data);

		$distributionProfile = $data->distributionProfile;
		$host = $distributionProfile->host;
		$port = $distributionProfile->port;
		$password = $distributionProfile->password;
		$basePath = $distributionProfile->basePath;

		$url = $host;
		$url .= $port ? ":$port" : "";
		$url .= "/" . $basePath;

		KalturaLog::debug("url - $url");

		$certLocation = $this->getFileLocationForCertificateKey($distributionProfile->id, $distributionProfile->certificateKey, self::CERT_KEY_FILE_NAME);

		$providerData = $data->providerData;
		$objectsForDistribution = $providerData->objectsForDistribution;

		foreach($objectsForDistribution as $distributionObject)
		{
			$distributionObject->contents = $this->transformJsonParam($distributionObject->contents, 'action', 'delete');

			$success = $this->distributeContent($distributionObject, $url, $certLocation, $password);
			if(!$success)
				return false;
		}

		return true;
	}
	
	public function transformJsonParam($jsonString, $name, $value)
	{
		$jsonArr = json_decode($jsonString);
		if(isset($jsonArr->$name))
			$jsonArr->$name = $value;
		else
			KalturaLog::notice("couldn't find $name in json string");
			
		return json_encode($jsonArr);
	}
	
	public function distributeContent($distributionObject, $url, $certFileLocation, $password)
	{
		if ($distributionObject->contents)
		{
			if ($distributionObject->type == 'metadata')
			{
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER ,array("Content-Type: application/json"));
				
				$postFields = array("data" => $distributionObject->contents);
				
				curl_setopt($ch, CURLOPT_CAINFO, $certFileLocation);
				curl_setopt($ch, CURLOPT_SSLCERT, $certFileLocation);
				curl_setopt($ch,CURLOPT_SSLCERTPASSWD, $password);
				
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
				
				KalturaLog::debug("distributing content - " . $distributionObject->contents);
				
				$result = curl_exec($ch);
				
				KalturaLog::debug("curl result - " . print_r($result, true));
				$decodedResult = json_decode($result);
				if(json_last_error() !== JSON_ERROR_NONE)
				{
						curl_close($ch);
						KalturaLog::err("json decode error with response - " . $result);
						return false;
				}
				
				if($decodedResult->status != "success")
					return false;
			}
		}
		else
			KalturaLog::err("no content");
			
		return true;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 */
	protected function validateJobObjects(KalturaDistributionJobData $data)
	{
		if(!$data->distributionProfile instanceof KalturaPushToNewsDistributionProfile)
			throw new Exception('Distribution profile must be of type KalturaPushToNewsDistributionProfile');
	
		if (!$data->providerData instanceof KalturaPushToNewsDistributionJobProviderData)
			throw new Exception('Provider data must be of type KalturaPushToNewsDistributionJobProviderData');
	}
	
		
	private function getFileLocationForCertificateKey($distributionProfileId, $keyContent, $fileName) 
	{
		$tempDirectory = $this->getTempDirectoryForProfile($distributionProfileId);
		$fileLocation = $tempDirectory . $fileName;
		if (!file_exists($fileLocation) || (file_get_contents($fileLocation) !== $keyContent))
		{
			file_put_contents($fileLocation, $keyContent);
			chmod($fileLocation, 0600);
		}
		
		return $fileLocation;
	}
    
	private function getTempDirectoryForProfile($distributionProfileId)
	{
		$tempFilePath = $this->tempDirectory . '/' . self::TEMP_DIRECTORY . '/' . $distributionProfileId . '/';
		if (!file_exists($tempFilePath))
			mkdir($tempFilePath, 0777, true);
		return $tempFilePath;
	}
}
