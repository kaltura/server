<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomTranscriptProcessor extends kZoomProcessor
{
	const ZOOM_TRANSCRIPT_FILE_EXT = 'vtt';
	const ZOOM_LABEL = 'Zoom';

	/**
	 * @param kZoomEvent $event
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingTranscriptComplete($event)
	{
		/* @var kZoomTranscriptCompleted $transcript */
		$transcript = $event->object;
		$zoomIntegration = ZoomHelper::getZoomIntegration();
		$dbUser = $this->getEntryOwner($transcript->hostEmail, $zoomIntegration);
		$this->initUserPermissions($dbUser);
		$entry = $this->getZoomEntryByRecordingId($transcript->uuid);
		if (!$entry)
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::MISSING_ENTRY_FOR_ZOOM_RECORDING . $transcript->uuid);
		}

		if ($this->isTranscriptionAlreadyHandled($entry))
		{
			KalturaLog::debug("Zoom transcription for entry {$entry->getId()} was already handled");
			return;
		}

		$this->initUserPermissions($dbUser, true);
		$captionAssetService = new CaptionAssetService();
		$captionAssetService->initService('caption_captionasset', 'captionAsset', 'setContent');
		$resourceReservation = new kResourceReservation(self::ZOOM_LOCK_TTL, true);
		foreach ($transcript->recordingFiles[kRecordingFileType::TRANSCRIPT] as $recordingFile)
		{
			/* @var kZoomRecordingFile $recordingFile */
			if (!$resourceReservation->reserve($recordingFile->id))
			{
				continue;
			}

			try
			{
				$captionAsset = $this->createAssetForTranscription($entry);
				$captionAssetResource = new KalturaUrlResource();
				$captionAssetResource->url = $recordingFile->download_url . self::URL_ACCESS_TOKEN . $event->downloadToken;
				$captionAssetService->setContentAction($captionAsset->getId(), $captionAssetResource);
			}
			catch (Exception $e)
			{
				ZoomHelper::exitWithError(kZoomErrorMessages::ERROR_HANDLING_TRANSCRIPT);
			}
		}

		$this->addRecordingTranscriptCompleteEntryTrack($entry);
	}

	/**
	 * @param entry $entry
	 * @return CaptionAsset
	 * @throws PropelException
	 */
	protected function createAssetForTranscription($entry)
	{
		$caption = new CaptionAsset();
		$caption->setEntryId($entry->getId());
		$caption->setPartnerId($entry->getPartnerId());
		$caption->setLanguage(KalturaLanguage::EN);
		$caption->setLabel(self::ZOOM_LABEL);
		$caption->setContainerFormat(CaptionType::WEBVTT);
		$caption->setStatus(CaptionAsset::ASSET_STATUS_QUEUED);
		$caption->setFileExt(self::ZOOM_TRANSCRIPT_FILE_EXT);
		$caption->setSource(CaptionSource::ZOOM);
		$caption->save();
		return $caption;
	}

	/**
	 * @param entry $entry
	 * @return bool
	 * @throws KalturaAPIException
	 */
	protected function isTranscriptionAlreadyHandled($entry)
	{
		$result = false;
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entry->getId();
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
		list($list, ) = $filter->doGetListResponse(new KalturaFilterPager(), $types);
		foreach($list as $captionAsset)
		{
			if($captionAsset->getSource() == CaptionSource::ZOOM)
			{
				$result = true;
				break;
			}
		}

		return $result;
	}

	/**
	 * @param entry $entry
	 */
	protected function addRecordingTranscriptCompleteEntryTrack($entry)
	{
		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($entry->getId());
		$trackEntry->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPDATE_ENTRY);
		$trackEntry->setDescription('Zoom Recording transcript Complete');
		TrackEntry::addTrackEntry($trackEntry);
	}
}