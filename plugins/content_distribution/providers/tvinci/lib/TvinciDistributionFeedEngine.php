<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage lib
 */
class TvinciDistributionFeedEngine extends DistributionEngine implements
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete
{
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaTvinciDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaTvinciDistributionProfile");

		if(!$data->providerData || !($data->providerData instanceof KalturaTvinciDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaTvinciDistributionJobProviderData");

		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);

		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaTvinciDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaTvinciDistributionProfile");

		if(!$data->providerData || !($data->providerData instanceof KalturaTvinciDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaTvinciDistributionJobProviderData");

		$this->handleUpdate($data, $data->distributionProfile, $data->providerData);

		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	*/
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaTvinciDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaTvinciDistributionProfile");

		if(!$data->providerData || !($data->providerData instanceof KalturaTvinciDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaTvinciDistributionJobProviderData");

		$this->handleDelete($data, $data->distributionProfile, $data->providerData);

		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		return false;
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaTvinciDistributionProfile $distributionProfile
	 * @param KalturaTvinciDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaTvinciDistributionProfile $distributionProfile, KalturaTvinciDistributionJobProviderData $providerData)
	{
		$url = $distributionProfile->ingestUrl;
		KalturaLog::info("Submitting entry {$data->entryDistribution->entryId}, url: $url\nXML data:\n{$providerData->xml}");

		$responseXml = $this->postXml($url, $providerData->xml);
		$success = ($responseXml->status == 'OK' || $responseXml->tvmID != '');
		if ( ! $success )
		{
			throw new Exception("Submit failed");
		}
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaTvinciDistributionProfile $distributionProfile
	 * @param KalturaTvinciDistributionJobProviderData $providerData
	 */
	protected function handleUpdate(KalturaDistributionJobData $data, KalturaTvinciDistributionProfile $distributionProfile, KalturaTvinciDistributionJobProviderData $providerData)
	{
		$url = $distributionProfile->ingestUrl;
		KalturaLog::info("Updating entry {$data->entryDistribution->entryId}, url: $url\nXML data:\n{$providerData->xml}");

		$responseXml = $this->postXml($url, $providerData->xml);
		$success = ($responseXml->status == 'OK' || $responseXml->tvmID != '');
		if ( ! $success )
		{
			throw new Exception("Update failed");
		}
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaTvinciDistributionProfile $distributionProfile
	 * @param KalturaTvinciDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaTvinciDistributionProfile $distributionProfile, KalturaTvinciDistributionJobProviderData $providerData)
	{
		$url = $distributionProfile->ingestUrl;
		KalturaLog::info("Deleting entry {$data->entryDistribution->entryId}, url: $url\nXML data:\n{$providerData->xml}");

		$responseXml = $this->postXml($url, $providerData->xml);
		$success = ($responseXml->status == 'OK' || $responseXml->tvmID != '');
		if ( ! $success )
		{
			throw new Exception("Delete failed");
		}
	}

	/**
	 * @param string $url
	 * @param string $xml
	 * @throws Exception in case of failure to receive a response
	 * @return SimpleXMLElement Sample content: <Response><status>ERROR</status><description>Root element is missing.</description><assetID></assetID><tvmID></tvmID></Response>
	 */
	protected function postXml($url, $xml)
	{
		$response = self::curlPost($url, $xml);
		KalturaLog::info("Full response: " . print_r($response,true));

		$responseXml = null;
		if ( $response['http_code'] == 200 )
		{
			$responseXml = simplexml_load_string( $response['content'] );
		}

		if ( !$responseXml )
		{
			throw new Exception("Failed parsing response"); // Throw an Exception in order to fail the job
		}

		return $responseXml;
	}
	

	public static function curlPost($url, $postData)
	{
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

		$content = curl_exec($ch);
		$curlError = curl_error($ch);
		$curlHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
	
		$response = array('content' => $content, 'http_code' => $curlHttpCode, 'error_text' => $curlError);

		return $response;
	}
}