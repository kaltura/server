<?php

/**
 * @package plugins.facebookDistribution
 * @subpackage lib
 */
class FacebookDistributionEngine extends DistributionEngine implements
	IDistributionEngineSubmit,
	IDistributionEngineDelete,
	IDistributionEngineUpdate
{
	protected $appId;
	protected $appSecret;

	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure()
	{
		parent::configure();
		$this->appId = kConf::get(FacebookRequestParameters::FACEBOOK_APP_ID_REQUEST_PARAM, 'facebook', null);
		$this->appSecret = kConf::get(FacebookRequestParameters::FACEBOOK_APP_SECRET_REQUEST_PARAM, 'facebook', null);

	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		$this->validate($data);
		if ($data->entryDistribution->remoteId) {
			$data->remoteId = $data->entryDistribution->remoteId;
		} else {
			$this->doSubmit($data);
		}
		return true;
	}

	protected function doSubmit(KalturaDistributionSubmitJobData $data)
	{
		$videoPath = $data->providerData->videoAssetFilePath;
		if (!$videoPath)
			throw new Exception('No video asset to distribute, the job will fail');
		if (!file_exists($videoPath))
			throw new KalturaDistributionException("The file [$videoPath] was not found (probably not synced yet), the job will retry");

		$facebookMetadata = $this->convertToFacebookData($data->providerData->fieldValues, true);

		try {
			/** on submission of a video we can take care of the following :
			 * 1. video content
			 * 2. thumbnail
			 * 3. call to action
			 * 4. name + description
			 * 5. place
			 * 6. tags
			 * 7. targeting
			 * 8. scheduled_publishing_time
			 * 9. feed targeting
			 */
			$data->remoteId = FacebookGraphSdkUtils::uploadVideo(
				$this->appId,
				$this->appSecret,
				$data->distributionProfile->pageId,
				$data->distributionProfile->pageAccessToken,
				$videoPath,
				$data->providerData->thumbAssetFilePath,
				filesize($videoPath),
				$this->tempDirectory,
				$facebookMetadata);
		} catch (Exception $e) {
			throw new Exception("Failed to submit facebook video , reason:".$e->getMessage());
		}
		return true;
//		foreach ($data->providerData->captionsInfo as $captionInfo)
//		{
//			if ($captionInfo->action == KalturaDistributionAction::SUBMIT)
//			{
//				$this->submitCaption($captionInfo, $data->remoteId);
//			}
//		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		$this->validate($data);
		return $this->doUpdate($data);
	}

	protected function doUpdate(KalturaDistributionUpdateJobData $data)
	{
		try {
			$facebookMetadata = $this->convertToFacebookData($data->providerData->fieldValues);

			FacebookGraphSdkUtils::updateUploadedVideo($this->appId,
				$this->appSecret,
				$data->distributionProfile->pageAccessToken,
				$facebookMetadata,
				$data->entryDistribution->remoteId);
		} catch (Exception $e) {
			throw new Exception("Failed to update facebook video , reason:".$e->getMessage());
		}

//		foreach ($data->providerData->captionsInfo as $captionInfo) {
//			switch ($captionInfo->action) {
//				case KalturaDistributionAction::SUBMIT:
//					$data->mediaFiles[] = $this->submitCaption($distributionProfile, $captionInfo, $data->entryDistribution->remoteId);
//					break;
//				case KalturaDistributionAction::DELETE:
//					$this->deleteCaption($distributionProfile, $captionInfo, $data->entryDistribution->remoteId);
//					break;
//			}
//		}
		return true;
	}

//	private function submitCaption(KalturaFacebookDistributionProfile $distributionProfile, KalturaCaptionDistributionInfo $captionInfo, $remoteId)
//	{
//		$status = FacebookGraphSdkUtils::uploadCaptions(
//			$this->appId,
//			$this->appSecret,
//			$distributionProfile->getPageAccessToken(),
//			$remoteId,
//			$captionInfo->filePath,
//			$captionInfo->language,
//			$this->tempDirectory);
//		return $status;
//	}
//
	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	*/
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		try {
			if ($data->entryDistribution->remoteId) {
				 FacebookGraphSdkUtils::deleteUploadedVideo(
					$this->appId,
					$this->appSecret,
					$data->distributionProfile->pageAccessToken,
					$data->entryDistribution->remoteId);
			} else
				throw new Exception(" Remote id is empty - nothing to delete");

		} catch (Exception $e) {
			throw new Exception("Failed to delete facebook video , reason:".$e->getMessage());
		}

		return true;
	}

	private function deleteCaption(KalturaFacebookDistributionProfile $distributionProfile, KalturaCaptionDistributionInfo $captionInfo, $remoteId)
	{
		$status = FacebookGraphSdkUtils::deleteCaptions(
			$this->appId,
			$this->appSecret,
			$distributionProfile->getPageAccessToken(),
			$remoteId,
			$captionInfo->language);

		return $status;
	}

	private function validate(KalturaDistributionJobData $data)
	{
		if (!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFacebookDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaFacebookDistributionProfile");

		if (!$this->appId)
			throw new Exception("Facebook appId is not configured");

		if (!$this->appSecret)
			throw new Exception("Facebook appSecret is not configured");
	}


	private function convertToFacebookData($fieldValues, $isSubmit=false)
	{
		$fieldValues = unserialize($fieldValues);
		$facebookMetadata = array();
		$facebookMetadata['title'] = $fieldValues[FacebookDistributionField::TITLE];
		$facebookMetadata['description'] = $fieldValues[FacebookDistributionField::DESCRIPTION];
		$callToActionType = $fieldValues[FacebookDistributionField::CALL_TO_ACTION_TYPE];
		if ($callToActionType)
		{
			$facebookMetadata['call_to_action'] =
			json_encode(
				array('type' => $callToActionType,
					'value' => array(
						'link' => $fieldValues[FacebookDistributionField::CALL_TO_ACTION_LINK],
						'link_caption' => $fieldValues[FacebookDistributionField::CALL_TO_ACTION_LINK_CAPTION]
					)));
		}
		if ($isSubmit) // these fields should not update
		{
			if ($fieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME])
			{
				$facebookMetadata['scheduled_publish_time'] = $fieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME];
				$facebookMetadata['published'] = 'false';
			}
		}
		return $facebookMetadata;
	}


}