<?php

require_once(__DIR__."/KalturaFacebookLanguageMatch.php");
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

	const FACEBOOK_CUSTOM_DATA_DELIMITER = ';';

	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure()
	{
		parent::configure();
		$this->appId = kConf::get(FacebookConstants::FACEBOOK_APP_ID_REQUEST_PARAM, 'facebook', null);
		$this->appSecret = kConf::get(FacebookConstants::FACEBOOK_APP_SECRET_REQUEST_PARAM, 'facebook', null);

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

		if ($data->providerData->captionsInfo)
		{
			foreach ($data->providerData->captionsInfo as $captionInfo)
			{
				$this->submitCaption($data->distributionProfile, $captionInfo, $data->remoteId);
			}
		}
		return true;
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

		foreach ($data->providerData->captionsInfo as $captionInfo) {
			switch ($captionInfo->action) {
				case KalturaDistributionAction::SUBMIT:
					$data->mediaFiles[] = $this->submitCaption($data->distributionProfile, $captionInfo, $data->entryDistribution->remoteId);
					break;
				case KalturaDistributionAction::DELETE:
					$this->deleteCaption($data->distributionProfile, $captionInfo, $data->entryDistribution->remoteId);
					break;
			}
		}
		return true;
	}

	private function submitCaption(KalturaFacebookDistributionProfile $distributionProfile, KalturaCaptionDistributionInfo $captionInfo, $remoteId)
	{
		if (!$captionInfo->label && !$captionInfo->language)
			throw new Exception("No label/language were configured for this caption aborting");
		if ($captionInfo->language)
			$locale = KalturaFacebookLanguageMatch::getFacebookCodeForKalturaLanguage($captionInfo->language);
		if (!$locale && $captionInfo->label)
			$locale = $captionInfo->label;
		if (!$locale)
			throw new Exception("Failed to find matching language for language ".$captionInfo->language." and there was no label available");
		$status = FacebookGraphSdkUtils::uploadCaptions(
			$this->appId,
			$this->appSecret,
			$distributionProfile->pageAccessToken,
			$remoteId,
			$captionInfo->filePath,
			$locale,
			$this->tempDirectory);
		return $status;
	}
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
		$facebookMetadata['name'] = $fieldValues[FacebookDistributionField::TITLE];
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
			if ($fieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME] &&
				$fieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME] > time())
			{
				$facebookMetadata['scheduled_publish_time'] = $fieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME];
				$facebookMetadata['published'] = 'false';
			}
			$targetingMetadata = array();
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'countries', $fieldValues[FacebookDistributionField::TARGETING_COUNTRIES], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'regions', $fieldValues[FacebookDistributionField::TARGETING_REGIONS], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'cities', $fieldValues[FacebookDistributionField::TARGETING_CITIES], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'zipcodes', $fieldValues[FacebookDistributionField::TARGETING_ZIP_CODES], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'excluded_countries', $fieldValues[FacebookDistributionField::TARGETING_EXCLUDED_COUNTRIES], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'excluded_regions', $fieldValues[FacebookDistributionField::TARGETING_EXCLUDED_REGIONS], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'excluded_cities', $fieldValues[FacebookDistributionField::TARGETING_EXCLUDED_CITIES], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'excluded_zipcodes', $fieldValues[FacebookDistributionField::TARGETING_EXCLUDED_ZIPCODES], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'timezones', $fieldValues[FacebookDistributionField::TARGETING_TIMEZONES], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'age_min', $fieldValues[FacebookDistributionField::TARGETING_AGE_MIN], false);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'age_max', $fieldValues[FacebookDistributionField::TARGETING_AGE_MAX], false);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'genders', $fieldValues[FacebookDistributionField::TARGETING_GENDERS], true);
			$this->insertTargetingFacebookMetadata($targetingMetadata, 'locales', $fieldValues[FacebookDistributionField::TARGETING_LOCALES], true);
			if (!empty($targetingMetadata))
				$facebookMetadata['targeting'] = json_encode($targetingMetadata);

			$feedTargetingMetadata = array();
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'countries', $fieldValues[FacebookDistributionField::FEED_TARGETING_COUNTRIES], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'regions', $fieldValues[FacebookDistributionField::FEED_TARGETING_REGIONS], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'cities', $fieldValues[FacebookDistributionField::FEED_TARGETING_CITIES], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'age_min', $fieldValues[FacebookDistributionField::FEED_TARGETING_AGE_MIN], false);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'age_max', $fieldValues[FacebookDistributionField::FEED_TARGETING_AGE_MAX], false);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'genders', $fieldValues[FacebookDistributionField::FEED_TARGETING_GENDERS], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'interested_in', $fieldValues[FacebookDistributionField::FEED_TARGETING_INTERESTED_IN], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'education_statuses', $fieldValues[FacebookDistributionField::FEED_TARGETING_EDUCATION_STATUSES], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'relationship_statuses', $fieldValues[FacebookDistributionField::FEED_TARGETING_RELATIONSHIP_STATUSES], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'college_years', $fieldValues[FacebookDistributionField::FEED_TARGETING_COLLEGE_YEARS], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'interests', $fieldValues[FacebookDistributionField::FEED_TARGETING_INTERESTS], true);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'relevant_until', $fieldValues[FacebookDistributionField::FEED_TARGETING_RELEVANT_UNTIL], false);
			$this->insertTargetingFacebookMetadata($feedTargetingMetadata, 'locales', $fieldValues[FacebookDistributionField::FEED_TARGETING_LOCALES], true);
			if (!empty($feedTargetingMetadata))
				$facebookMetadata['feed_targeting'] = json_encode($feedTargetingMetadata);
		}
		KalturaLog::info("Facebook metadata constructed as : ".print_r($facebookMetadata, true));
		return $facebookMetadata;
	}

	private function insertTargetingFacebookMetadata(&$targetingArray, $key, $value, $isArray)
	{
		if ($value)
		{
			if($isArray)
			{
				if (strpos($value, self::FACEBOOK_CUSTOM_DATA_DELIMITER) !== false)
				{
					$targetingArray[$key] = explode(self::FACEBOOK_CUSTOM_DATA_DELIMITER, $value);
				} else {
					$targetingArray[$key] = array($value);
				}
			} else {
				$targetingArray[$key] = $value;
			}
		}
	}

}