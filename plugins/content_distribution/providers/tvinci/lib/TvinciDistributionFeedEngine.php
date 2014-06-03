<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage lib
 */
class TvinciDistributionFeedEngine extends DistributionEngine implements
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseUpdate,
	IDistributionEngineCloseSubmit,
	IDistributionEngineCloseDelete
{
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaTvinciDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaTvinciDistributionProfile");

		if(!$data->providerData || !($data->providerData instanceof KalturaTvinciDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaTvinciDistributionJobProviderData");

		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);

		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaTvinciDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaTvinciDistributionProfile");

		if(!$data->providerData || !($data->providerData instanceof KalturaTvinciDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaTvinciDistributionJobProviderData");

		$this->handleUpdate($data, $data->distributionProfile, $data->providerData);

		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	*/
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaTvinciDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaTvinciDistributionProfile");

		if(!$data->providerData || !($data->providerData instanceof KalturaTvinciDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaTvinciDistributionJobProviderData");

		$this->handleDelete($data, $data->distributionProfile, $data->providerData);

		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	*/
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
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
		$xml = $providerData->submitXml;
		KalturaLog::info("Submitting entry {$data->entryDistribution->entryId}, url: $url\nXML data:\n$xml");

		$responseXml = $this->postXml($url, $xml);
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
		$xml = $providerData->updateXml;
		KalturaLog::info("Updating entry {$data->entryDistribution->entryId}, url: $url\nXML data:\n$xml");

		$responseXml = $this->postXml($url, $xml);
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
		$xml = $providerData->deleteXml;
		KalturaLog::info("Deleting entry {$data->entryDistribution->entryId}, url: $url\nXML data:\n$xml");

		$responseXml = $this->postXml($url, $xml);
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
		$options = array( 'post_data' => $xml, 'full_response' => true );
		$fullResponse = KCurlWrapper::getContent($url, $options);
		KalturaLog::info("Full response: " . print_r($fullResponse,true));

		$responseXml = null;
		if ( $fullResponse['http_code'] == 200 )
		{
			$responseXml = simplexml_load_string( $fullResponse['content'] );
		}

		if ( !$responseXml )
		{
			throw new Exception("Failed parsing response"); // Throw an Exception in order to fail the job
		}

		return $responseXml;
	}
}